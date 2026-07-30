<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Enums\Role;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;

class InspectionController extends Controller
{

    public function index()
    {
        // ดึงกลุ่มฟอร์มที่เปิดใช้งาน (Master Forms)
        $formGroups = DB::table('form_groups')
            ->where('is_active', 1)
            ->whereNull('deleted_at')
            ->get();

        return view('pages.inspection.step1_select', compact('formGroups'));
    }


    // Vehicle search 
    public function searchVehicle(Request $request)
    {
        $user = Auth::user()->user_id;
        $search = $request->get('q');

        // 1. Get inspector profile
        $inspector = DB::table('inspector_datas')
            ->where('ins_id', '=', $user)
            ->first();

        if (!$inspector) {
            return response()->json([]);
        }

        // 2. Base query
        $query = DB::table('vehicles_detail')
            ->where('vehicles_detail.car_plate', 'LIKE', "%{$search}%")
            ->where('vehicles_detail.status', '!=', '2');

        // ==========================================
        // Data Access Logic (role-based filtering)
        // ==========================================
        if ($inspector->inspector_type == 1) {
            $query->where('vehicles_detail.company_code', $inspector->company_code);
        } elseif ($inspector->inspector_type == 2) {
            $query->where('vehicles_detail.company_code', $inspector->company_code)
                ->where('vehicles_detail.supply_id', $inspector->sup_id);
        } elseif ($inspector->inspector_type == 3) {
            $allowedSupplyIds = DB::table('inspector_supply_access')
                ->where('ins_id', $inspector->ins_id)
                ->pluck('supply_id');

            $query->where('vehicles_detail.company_code', $inspector->company_code)
                ->whereIn('vehicles_detail.supply_id', $allowedSupplyIds);
        }

        // 3. Get candidates - check eligibility in PHP for readability
        $candidates = $query->limit(30)->get([
            'vehicles_detail.car_id',
            'vehicles_detail.car_plate',
            'vehicles_detail.car_brand',
            'vehicles_detail.car_model'
        ]);

        // 4. Build response with eligibility info
        //    - 'allowed' vehicles: selectable (normal display)
        //    - 'locked' vehicles: shown but disabled (with inspector name)
        //    - 'blocked' vehicles: filtered out (already passed / cycle full)
        $results = [];
        foreach ($candidates as $v) {
            $eligibility = $this->checkInspectionEligibility($v->car_id);

            // Skip blocked vehicles entirely (already passed, or cycle full)
            if ($eligibility['status'] === 'blocked') {
                continue;
            }

            $results[] = [
                'car_id'         => $v->car_id,
                'car_plate'      => $v->car_plate,
                'car_brand'      => $v->car_brand,
                'car_model'      => $v->car_model,
                'is_locked'      => $eligibility['status'] === 'locked',
                'lock_message'   => $eligibility['status'] === 'locked' ? $eligibility['reason'] : null,
                'inspector_name' => $eligibility['inspector_name'] ?? null,
            ];

            if (count($results) >= 10) break;
        }

        return response()->json($results);
    }

    // กดปุ่มเริ่มตรวจ (สร้าง Record)
    // Start a new inspection (create chk_records as draft)
    public function start(Request $request)
    {
        $user = Auth::user()->user_id;
        $request->validate([
            'car_id' => 'required',
            'form_group_id' => 'required'
        ]);

        // ==========================================
        // SECURITY CHECK: Eligibility validation
        // (Server-side guard - even if frontend bypassed)
        // ==========================================
        $eligibility = $this->checkInspectionEligibility($request->car_id);

        if ($eligibility['status'] === 'locked') {
            return back()->with(
                'error',
                'ไม่สามารถเริ่มตรวจรถคันนี้ได้ เนื่องจาก' . $eligibility['reason']
            );
        }

        if ($eligibility['status'] === 'blocked') {
            return back()->with('error', $eligibility['reason']);
        }

        // 1. Get inspector data
        $inspector = DB::table('inspector_datas')->where('ins_id', $user)->first();

        // 2. Get vehicle data (for supply_id snapshot)
        $car = DB::table('vehicles_detail')->where('car_id', $request->car_id)->first();

        // 3. Get form group
        $formGroup = DB::table('form_groups')->where('id', $request->form_group_id)->first();

        // 4. Generate record ID
        $generateRecordId = 'CHK-' . date('Ymd-His');

        // 5. Determine round number (for future use in step3 / report)
        $roundNumber = $this->getCurrentRoundNumber($request->car_id);

        // 6. Create draft record
        DB::table('chk_records')->insertGetId([
            'user_id'       => $user,
            'veh_id'        => $request->car_id,
            'record_id'     => $generateRecordId,
            'form_group_id' => $formGroup->form_group_id,
            'form_id'       => $formGroup->check_item_form_id,
            'supply_id'     => $car->supply_id ?? '',
            'chk_status'    => '0',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        return redirect()->route('inspection.step2', ['record_id' => $generateRecordId]);
    }

    // ตรวจอีกครั้ง: ข้าม step1 สร้าง record ใหม่โดยดึง form_group จาก record เดิม
    public function retest($record_id)
    {
        $user = Auth::user()->user_id;
        $previousRecord = DB::table('chk_records')->where('record_id', $record_id)->first();

        if (!$previousRecord) {
            return redirect()->route('ins-dashboard')->with('error', 'ไม่พบข้อมูลการตรวจ');
        }

        $eligibility = $this->checkInspectionEligibility($previousRecord->veh_id);

        if ($eligibility['status'] === 'locked') {
            return redirect()->route('ins-dashboard')->with('error', 'ไม่สามารถเริ่มตรวจได้ เนื่องจาก' . $eligibility['reason']);
        }

        if ($eligibility['status'] === 'blocked') {
            return redirect()->route('ins-dashboard')->with('error', $eligibility['reason']);
        }

        $car = DB::table('vehicles_detail')->where('car_id', $previousRecord->veh_id)->first();
        $newRecordId = 'CHK-' . date('Ymd-His');

        DB::table('chk_records')->insert([
            'user_id'       => $user,
            'veh_id'        => $previousRecord->veh_id,
            'record_id'     => $newRecordId,
            'form_group_id' => $previousRecord->form_group_id,
            'form_id'       => $previousRecord->form_id,
            'supply_id'     => $car->supply_id ?? '',
            'chk_status'    => '0',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        return redirect()->route('inspection.step2', ['record_id' => $newRecordId]);
    }

    //---------------------STEP2-----------------//

    public function step2($record_id)
    {
        // 1. Get inspection record
        $record = DB::table('chk_records')->where('record_id', $record_id)->first();
        if (!$record) return redirect()->route('inspection.index')->with('error', 'ไม่พบประวัติการตรวจนี้');

        $formGroup = DB::table('form_groups')->where('form_group_id', $record->form_group_id)->first();

        // 2. Get vehicle data for editable info card (tax / register date / insurance)
        $vehicle = DB::table('vehicles_detail as v')
            ->leftJoin('insurance_companies as ic', 'v.car_insurance_id', '=', 'ic.id')
            ->where('v.car_id', $record->veh_id)
            ->select('v.id', 'v.car_id', 'v.car_plate', 'v.car_trailer_plate', 'v.car_brand', 'v.car_tax',
                     'v.car_register_date', 'v.car_insurance_id', 'v.car_insurance_start',
                     'v.car_insurance_expire', 'ic.name as insurance_company_name')
            ->first();

        $insuranceCompanies = DB::table('insurance_companies')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $carBrands = DB::table('car_brands')->orderBy('brand_name')->get();

        // 3. If no pre-inspection template, skip directly to step 3
        if (empty($formGroup->pre_inspection_template_id)) {
            return redirect()->route('inspection.step3', ['record_id' => $record_id]);
        }

        $preFields = DB::table('pre_inspection_fields')
            ->where('template_id', $formGroup->pre_inspection_template_id)
            ->orderBy('sort_order')
            ->get();

        // ดึงข้อมูลที่กรอกไว้ในรอบก่อนหน้า (ถ้ามี) เพื่อ pre-fill ใน retest
        $previousPreData = collect();
        $previousPreRecordId = null;
        $previousRecord = DB::table('chk_records')
            ->where('veh_id', $record->veh_id)
            ->where('chk_status', '1')
            ->where('record_id', '!=', $record_id)
            ->orderBy('id', 'desc')
            ->first();

        if ($previousRecord) {
            $previousPreRecordId = $previousRecord->record_id;
            $previousPreData = DB::table('pre_inspection_results')
                ->where('record_id', $previousRecord->record_id)
                ->get()
                ->keyBy('field_id');
        }

        return view('pages.inspection.step2_pre_inspect', compact(
            'record', 'formGroup', 'preFields', 'vehicle', 'insuranceCompanies',
            'carBrands', 'previousPreData', 'previousPreRecordId'
        ));
    }

    public function storeStep2(Request $request, $record_id)
    {
        $record = DB::table('chk_records')->where('record_id', $record_id)->first();
        $user = DB::table('users')->where('user_id', Auth::user()->user_id)->first();

        // 1. Save photos -> pre_inspection_results (keyed by field_id)
        if ($request->hasFile('photos')) {
            $photoData = [];
            foreach ($request->file('photos') as $fieldId => $file) {
                $filename = 'pre_img_' . $fieldId . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public/pre_inspections/' . $record->record_id, $filename);
                $photoData[] = [
                    'record_id'   => $record->record_id,
                    'field_id'    => $fieldId,
                    'field_value' => 'storage/pre_inspections/' . $record->record_id . '/' . $filename,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];
            }
            if (!empty($photoData)) {
                DB::table('pre_inspection_results')->insert($photoData);
            }
        }

        // 2. Save document fields
        if ($request->hasFile('docs')) {
            $docData = [];
            foreach ($request->file('docs') as $fieldId => $file) {
                if ($file->getSize() > 10 * 1024 * 1024 || $file->getClientOriginalExtension() !== 'pdf') {
                    continue;
                }
                $filename = 'doc_' . $fieldId . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public/pre_inspections/' . $record->record_id, $filename);
                $docData[] = [
                    'record_id'   => $record->record_id,
                    'field_id'    => $fieldId,
                    'field_value' => 'storage/pre_inspections/' . $record->record_id . '/' . $filename,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];
            }
            if (count($docData) > 0) {
                DB::table('pre_inspection_results')->insert($docData);
            }
        }

        // 3. Save text/GPS fields (existing logic - unchanged)
        if ($request->has('fields')) {
            $dynamicData = [];
            foreach ($request->fields as $field_id => $value) {
                if (!empty($value)) {
                    $dynamicData[] = [
                        'record_id'   => $record->record_id,
                        'field_id'    => $field_id,
                        'field_value' => $value,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ];
                }
            }
            if (count($dynamicData) > 0) {
                DB::table('pre_inspection_results')->insert($dynamicData);
            }
        }

        // 3. Update vehicle info (tax / register date / insurance) + write activity log
        //    JS already converted Buddhist year to Christian year (Y-m-d format).
        //    Server validates again to prevent bypass via direct POST.
        if ($request->has('vehicle_info')) {
            $vehicle = DB::table('vehicles_detail')->where('car_id', $record->veh_id)->first();

            if ($vehicle) {
                $updatePayload = [];
                $beforeChanged = [];
                $afterChanged  = [];

                // Handle car_brand (text field)
                $newBrand = trim($request->input('vehicle_info.car_brand') ?? '');
                $oldBrand = trim($vehicle->car_brand ?? '');
                if ($newBrand !== '' && $oldBrand !== $newBrand) {
                    $updatePayload['car_brand'] = $newBrand;
                    $beforeChanged['car_brand'] = $oldBrand;
                    $afterChanged['car_brand']  = $newBrand;
                }

                // Handle car_model (text field)
                $newModel = trim($request->input('vehicle_info.car_model') ?? '');
                $oldModel = trim($vehicle->car_model ?? '');
                if ($newModel !== '' && $oldModel !== $newModel) {
                    $updatePayload['car_model'] = $newModel;
                    $beforeChanged['car_model'] = $oldModel;
                    $afterChanged['car_model']  = $newModel;
                }

                // Handle insurance company (integer, not a date)
                if ($request->has('vehicle_info.car_insurance_id')) {
                    $newInsId = $request->input('vehicle_info.car_insurance_id');
                    $newInsId = ($newInsId !== '' && $newInsId !== null) ? (int) $newInsId : null;
                    $oldInsId = $vehicle->car_insurance_id ? (int) $vehicle->car_insurance_id : null;
                    if ($oldInsId !== $newInsId) {
                        $updatePayload['car_insurance_id'] = $newInsId;
                        $beforeChanged['car_insurance_id'] = $oldInsId;
                        $afterChanged['car_insurance_id']  = $newInsId;
                    }
                }

                // Handle date fields
                $dateFields = ['car_tax', 'car_register_date', 'car_insurance_expire'];
                foreach ($dateFields as $col) {
                    $newValue = $request->input("vehicle_info.{$col}");

                    // Skip if user didn't fill the field (keep existing value)
                    if ($newValue === null || $newValue === '') {
                        continue;
                    }

                    // Server-side validation: must be valid Y-m-d format
                    try {
                        $newNorm = \Carbon\Carbon::createFromFormat('Y-m-d', $newValue)->format('Y-m-d');
                    } catch (\Exception $e) {
                        continue;
                    }

                    // Compare with existing value
                    $oldValue = $vehicle->{$col};
                    $oldNorm  = $oldValue ? \Carbon\Carbon::parse($oldValue)->format('Y-m-d') : null;

                    if ($oldNorm !== $newNorm) {
                        $updatePayload[$col] = $newNorm;
                        $beforeChanged[$col] = $oldNorm;
                        $afterChanged[$col]  = $newNorm;
                    }
                }

                // Only proceed if there are actual changes
                if (!empty($updatePayload)) {
                    $updatePayload['updated_at'] = now();

                    DB::table('vehicles_detail')
                        ->where('car_id', $record->veh_id)
                        ->update($updatePayload);

                    // Write activity log (only fields that actually changed)
                    DB::table('vehicle_activity_logs')->insert([
                        'vehicle_id'  => $vehicle->id,
                        'user_id'     => Auth::user()->user_id,
                        'action'      => 'update',
                        'before_data' => json_encode($beforeChanged, JSON_UNESCAPED_UNICODE),
                        'after_data'  => json_encode($afterChanged, JSON_UNESCAPED_UNICODE),
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ]);
                }
            }
        }

        // สำหรับ retest: ถ้า field ภาพ/เอกสารไม่ได้ re-upload ให้ copy path จากรอบก่อน
        $prevRecordId = $request->input('previous_record_id');
        if ($prevRecordId) {
            if ($request->has('keep_photos')) {
                foreach ($request->keep_photos as $fieldId => $val) {
                    $alreadyUploaded = $request->hasFile('photos') && isset($request->file('photos')[$fieldId]);
                    if (!$alreadyUploaded) {
                        $prevResult = DB::table('pre_inspection_results')
                            ->where('record_id', $prevRecordId)
                            ->where('field_id', $fieldId)
                            ->first();
                        if ($prevResult) {
                            DB::table('pre_inspection_results')->insertOrIgnore([
                                'record_id'   => $record->record_id,
                                'field_id'    => $fieldId,
                                'field_value' => $prevResult->field_value,
                                'created_at'  => now(),
                                'updated_at'  => now(),
                            ]);
                        }
                    }
                }
            }

            if ($request->has('keep_docs')) {
                foreach ($request->keep_docs as $fieldId => $val) {
                    $alreadyUploaded = $request->hasFile('docs') && isset($request->file('docs')[$fieldId]);
                    if (!$alreadyUploaded) {
                        $prevResult = DB::table('pre_inspection_results')
                            ->where('record_id', $prevRecordId)
                            ->where('field_id', $fieldId)
                            ->first();
                        if ($prevResult) {
                            DB::table('pre_inspection_results')->insertOrIgnore([
                                'record_id'   => $record->record_id,
                                'field_id'    => $fieldId,
                                'field_value' => $prevResult->field_value,
                                'created_at'  => now(),
                                'updated_at'  => now(),
                            ]);
                        }
                    }
                }
            }
        }

        // Save trailer plate to this inspection record
        DB::table('chk_records')->where('record_id', $record_id)->update([
            'trailer_plate' => $request->input('trailer_plate') ?: null,
        ]);

        return redirect()->route('inspection.step3', ['record_id' => $record_id]);
    }

    // โหลดหน้า Step 3
    public function step3(Request $request, $record_id)
    {
        $record = DB::table('chk_records')->where('record_id', $record_id)->first();
        if (!$record) return redirect()->route('inspection.index');

        $formGroup = DB::table('form_groups')->where('form_group_id', $record->form_group_id)->first();

        $categories = DB::table('check_categories')
            ->where('form_id', $record->form_id)
            ->orderBy('cates_no', 'asc')
            ->get();

        $currentCategoryId = $request->get('cat_id', $categories->first()->category_id ?? null);
        $items = DB::table('check_items')->where('category_id', $currentCategoryId)->get();

        $existingResults = DB::table('check_records_result')
            ->where('record_id', $record->record_id)
            ->get()
            ->keyBy('item_id');

        $existingImages = DB::table('check_result_images')
            ->where('record_id', $record->record_id)
            ->get()
            ->groupBy('item_id');

        $vehicle = DB::table('vehicles_detail')->where('car_id', $record->veh_id)->first();

        // ==========================================
        // Retest context: identify items that should be readonly (already passed)
        // vs items that need re-inspection (failed in previous round)
        // ==========================================
        $retestContext = $this->getRetestContext($record->veh_id, $record->record_id);
        $isRetestRound = $retestContext !== null;
        $passedItemIds = $isRetestRound ? $retestContext['passed_item_ids'] : [];
        $retestItemIds = $isRetestRound ? $retestContext['retest_item_ids'] : [];
        $previousResults = $isRetestRound ? $retestContext['previous_round_results'] : collect();
        $previousImages = collect();
        if ($isRetestRound && !empty($retestItemIds)) {
            $previousImages = DB::table('check_result_images')
                ->where('record_id', $retestContext['previous_record_id'])
                ->whereIn('item_id', $retestItemIds)
                ->get()
                ->groupBy('item_id');
        }

        // Auto-insert "passed" results from previous round into current record (once).
        // Uses updateOrInsert (not raw insert) to be idempotent — safe against concurrent
        // requests (two tabs, browser reload) that could otherwise create duplicate rows
        // since check_records_result has no DB-level unique constraint on (record_id, item_id).
        if ($isRetestRound && !empty($passedItemIds)) {
            foreach ($passedItemIds as $itemId) {
                if (!$existingResults->has($itemId)) {
                    $prev = $previousResults->get($itemId);
                    DB::table('check_records_result')->updateOrInsert(
                        ['record_id' => $record->record_id, 'item_id' => $itemId],
                        [
                            'user_id'       => $record->user_id,
                            'result_status' => '1',
                            'result_value'  => $prev->result_value ?? null,
                            'user_comment'  => $prev->user_comment ?? null,
                            'updated_at'    => now(),
                        ]
                    );
                }
            }
            // Refresh existing results after insertion
            $existingResults = DB::table('check_records_result')
                ->where('record_id', $record->record_id)
                ->get()
                ->keyBy('item_id');
        }

        // ==========================================
        // Calculate progress per category (for tab dots)
        // - Round 1: total = all items, checked = items with result
        // - Round 2/3: total = items requiring retest only
        //   (passed items are auto-inserted above so they don't need to be counted)
        // ==========================================
        $categoryProgress = [];
        foreach ($categories as $cat) {
            if ($isRetestRound) {
                // Only count items that need re-inspection in this round
                $itemsToCheck = DB::table('check_items')
                    ->where('category_id', $cat->category_id)
                    ->whereIn('item_id', $retestItemIds)
                    ->pluck('item_id');

                $totalItems = $itemsToCheck->count();

                $checkedItems = DB::table('check_records_result')
                    ->where('record_id', $record->record_id)
                    ->whereIn('item_id', $itemsToCheck)
                    ->whereNotNull('result_status')
                    ->count();
            } else {
                // Round 1: count all items in this category
                $totalItems = DB::table('check_items')
                    ->where('category_id', $cat->category_id)
                    ->count();

                $checkedItems = DB::table('check_records_result')
                    ->join('check_items', 'check_records_result.item_id', '=', 'check_items.item_id')
                    ->where('check_records_result.record_id', $record->record_id)
                    ->where('check_items.category_id', $cat->category_id)
                    ->whereNotNull('check_records_result.result_status')
                    ->count();
            }

            if ($totalItems === 0) {
                $status = 'complete'; // Nothing to check = complete (relevant in retest)
            } elseif ($checkedItems === 0) {
                $status = 'empty';
            } elseif ($checkedItems >= $totalItems) {
                $status = 'complete';
            } else {
                $status = 'partial';
            }

            $categoryProgress[$cat->category_id] = [
                'total'   => $totalItems,
                'checked' => $checkedItems,
                'status'  => $status,
            ];
        }

        // Round number for display (1, 2, or 3)
        $roundNumber = DB::table('chk_records')
            ->where('veh_id', $record->veh_id)
            ->where('id', '<=', $record->id)
            ->where(function ($q) {
                $q->where('chk_status', '1')->orWhere('chk_status', '0')->orWhere('chk_status', '2');
            })
            ->count();

        // Use cycle-based count if available
        $cycleFirst = $this->getCycleFirstRecord($record->veh_id);
        if ($cycleFirst && $record->id >= $cycleFirst->id) {
            $roundNumber = DB::table('chk_records')
                ->where('veh_id', $record->veh_id)
                ->where('id', '>=', $cycleFirst->id)
                ->where('id', '<=', $record->id)
                ->count();
        } else {
            $roundNumber = 1;
        }

        return view('pages.inspection.step3_checklist', compact(
            'record',
            'formGroup',
            'categories',
            'currentCategoryId',
            'items',
            'existingResults',
            'existingImages',
            'vehicle',
            'categoryProgress',
            'isRetestRound',
            'passedItemIds',
            'retestItemIds',
            'roundNumber',
            'previousResults',
            'previousImages'
        ));
    }

    // Auto-save result via AJAX
    public function saveResult(Request $request)
    {
        $user = Auth::user()->user_id;
        $data = $request->all();

        // ==========================================
        // Server-side guard: block saves on readonly items in retest rounds
        // ==========================================
        $record = DB::table('chk_records')->where('record_id', $data['record_id'])->first();
        if ($record) {
            $retestContext = $this->getRetestContext($record->veh_id, $record->record_id);
            if ($retestContext && in_array($data['item_id'], $retestContext['passed_item_ids'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'ข้อนี้ผ่านการตรวจในรอบก่อนแล้ว ไม่สามารถแก้ไขได้'
                ], 422);
            }
        }

        DB::table('check_records_result')->updateOrInsert(
            [
                'record_id' => $data['record_id'],
                'item_id'   => $data['item_id']
            ],
            [
                'user_id'       => $user,
                'result_status' => $data['result_status'] ?? null,
                'result_value'  => $data['result_value'] ?? null,
                'user_comment'  => $data['user_comment'] ?? null,
                'updated_at'    => now()
            ]
        );

        return response()->json(['status' => 'success']);
    }

    // อัปโหลดรูปภาพย่อยผ่าน AJAX (จำกัด 10 ภาพ)
    public function uploadItemImage(Request $request)
    {
        $request->validate([
            'image'     => 'required|image|max:5120', // ขนาดไม่เกิน 5MB
            'record_id' => 'required',
            'item_id'   => 'required'
        ]);

        // เช็คว่าอัปโหลดเกิน 10 ภาพหรือยัง
        $imgCount = DB::table('check_result_images')
            ->where('record_id', $request->record_id)
            ->where('item_id', $request->item_id)
            ->count();

        if ($imgCount >= 10) {
            return response()->json(['status' => 'error', 'message' => 'อัปโหลดได้สูงสุด 10 ภาพต่อข้อ'], 422);
        }

        // บันทึกไฟล์ (ปรับ path ตามโครงสร้างโปรเจกต์ของคุณ)
        $file = $request->file('image');
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('public/fileupload/' . $request->record_id, $filename);
        $dbPath = 'storage/fileupload/' . $request->record_id . '/' . $filename;

        // บันทึกลงตาราง check_result_images
        $imageId = DB::table('check_result_images')->insertGetId([
            'record_id'  => $request->record_id,
            'item_id'    => $request->item_id,
            'image_path' => $dbPath,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success'   => true,
            'status' => 'success',
            'image_id' => $imageId,
            'image_url' => asset($dbPath)
        ]);
    }

    // ลบรูปภาพย่อย
    public function deleteItemImage(Request $request)
    {
        $imageId = $request->image_id;
        $image = DB::table('check_result_images')->where('id', $imageId)->first();

        if ($image) {
            \Illuminate\Support\Facades\Storage::delete(str_replace('storage/', 'public/', $image->image_path));
            DB::table('check_result_images')->where('id', $imageId)->delete();
        }
        return response()->json([
            'success' => true,
            'status'  => 'success',
            'message' => 'ลบรูปภาพเรียบร้อย'
        ]);
    }

    //---------------------Step4-------------------//

    public function step4($record_id)
    {
        $record = DB::table('chk_records')->where('record_id', $record_id)->first();
        if (!$record) return redirect()->route('inspection.index');

        $totalItems = DB::table('check_items')
            ->join('check_categories', 'check_items.category_id', '=', 'check_categories.category_id')
            ->where('check_categories.form_id', $record->form_id)
            ->count();

        // Deduplicate by item_id as a safety net for any pre-existing duplicate rows
        // (no DB-level unique constraint on (record_id, item_id)).
        $results = DB::table('check_records_result')
            ->where('record_id', $record->record_id)
            ->get()
            ->unique('item_id');

        $passCount    = $results->where('result_status', '1')->count();
        $failCount    = $results->where('result_status', '0')->count();
        $AlmostCount  = $results->where('result_status', '2')->count();
        $uncheckedCount = $totalItems - ($passCount + $failCount + $AlmostCount);

        $formGroup = DB::table('form_groups')->where('form_group_id', $record->form_group_id)->first();
        $vehicle = DB::table('vehicles_detail')->where('car_id', $record->veh_id)->first();

        // Inspector profile signature (used as default if exists, regardless of round)
        $user = DB::table('users')->where('user_id', $record->user_id)->first();
        $inspectorProfileSign = $user->signature_image ?? null;

        return view('pages.inspection.step4_summary', compact(
            'record',
            'totalItems',
            'passCount',
            'failCount',
            'uncheckedCount',
            'formGroup',
            'vehicle',
            'AlmostCount',
            'inspectorProfileSign'
        ));
    }

    // บันทึกการตรวจ
    public function submitInspection(Request $request, $record_id)
    {
        // 1. Load record
        $record = DB::table('chk_records')->where('record_id', $record_id)->first();
        if (!$record) return redirect()->back()->with('error', 'ไม่พบข้อมูลใบตรวจ');

        // 2. Validation by submit type
        if ($request->submit_type == 'final') {
            $request->validate([
                'evaluate_status' => 'required',
            ]);

            // If using live canvas, sign data is required
            // If using profile sign (use_profile_sign=1), server will copy from users.signature_image
            if ($request->use_profile_sign != '1' && !$request->filled('inspector_sign_data')) {
                return back()->with('error', 'กรุณาลงลายมือชื่อผู้ตรวจ');
            }

            // Status 3 requires next inspection date
            if ($request->evaluate_status == '3') {
                $request->validate([
                    'next_inspect_date' => 'required|date'
                ]);
            }
        }

        // 3. Resolve inspector signature path
        //    Priority: live canvas (if signed) > copy from user profile > keep existing
        $inspectorSignPath = $record->inspector_sign;

        if ($request->filled('inspector_sign_data')) {
            // Case A: Inspector signed live on canvas → save base64 as new file
            $image_parts = explode(";base64,", $request->inspector_sign_data);
            if (count($image_parts) == 2) {
                $image_base64 = base64_decode($image_parts[1]);
                $filename = 'sign_ins_' . $record->record_id . '_' . time() . '.png';
                $path = 'public/signatures/' . $filename;
                \Illuminate\Support\Facades\Storage::put($path, $image_base64);
                $inspectorSignPath = 'storage/signatures/' . $filename;
            }
        } elseif ($request->use_profile_sign == '1') {
            // Case B: Inspector kept profile signature → copy from users.signature_image
            // Snapshot per record (so changing profile signature later won't affect old records)
            $user = DB::table('users')->where('user_id', $record->user_id)->first();
            if ($user && !empty($user->signature_image)) {
                $sourcePath = public_path($user->signature_image);
                if (file_exists($sourcePath)) {
                    $ext = pathinfo($sourcePath, PATHINFO_EXTENSION) ?: 'png';
                    $filename = 'sign_ins_' . $record->record_id . '_' . time() . '.' . $ext;
                    $destRelative = 'public/signatures/' . $filename;
                    \Illuminate\Support\Facades\Storage::put($destRelative, file_get_contents($sourcePath));
                    $inspectorSignPath = 'storage/signatures/' . $filename;
                } else {
                    // Fallback: profile path stored in DB but file missing → reuse DB path as-is
                    $inspectorSignPath = $user->signature_image;
                }
            }
        }
        // else: draft save without any signature change → keep existing

        // 4. Driver signature (always live canvas)
        $driverSignPath = $record->driver_sign;
        if ($request->filled('driver_sign_data')) {
            $image_parts = explode(";base64,", $request->driver_sign_data);
            if (count($image_parts) == 2) {
                $image_base64 = base64_decode($image_parts[1]);
                $filename = 'sign_drv_' . $record->record_id . '_' . time() . '.png';
                $path = 'public/signatures/' . $filename;
                \Illuminate\Support\Facades\Storage::put($path, $image_base64);
                $driverSignPath = 'storage/signatures/' . $filename;
            }
        }

        // 5. Build update payload
        $updateData = [
            'inspector_sign' => $inspectorSignPath,
            'driver_sign'    => $driverSignPath,
            'updated_at'     => now(),
        ];

        if ($request->filled('inspect_date')) {
            $updateData['inspect_date'] = $request->inspect_date;
        }

        if ($request->has('evaluate_status')) {
            $updateData['evaluate_status'] = $request->evaluate_status;
            if ($request->evaluate_status == '3') {
                $updateData['next_inspect_date'] = $request->next_inspect_date;
            } else {
                $updateData['next_inspect_date'] = null;
            }
        }

        // 6. Draft vs Final
        if ($request->submit_type == 'final') {
            $updateData['chk_status'] = '1';
            $message = 'บันทึกและยืนยันผลการตรวจรถเรียบร้อยแล้ว';
        } else {
            $updateData['chk_status'] = '2';
            $message = 'บันทึกแบบร่างสำเร็จ คุณสามารถกลับมาตรวจต่อได้ในภายหลัง';
        }

        // 7. Update
        DB::table('chk_records')->where('record_id', $record_id)->update($updateData);

        return redirect()->route('inspection.report', ['record_id' => $record_id])->with('success', $message);
    }

    //----------Report--------------//
    public function viewReport($record_id)
    {
        // 1. ดึงข้อมูลบันทึกการตรวจหลัก
        $record = DB::table('chk_records')->where('record_id', $record_id)->first();
        if (!$record) return redirect()->back()->with('error', 'ไม่พบข้อมูล');

        // 2. ดึงข้อมูล Form Group เพื่อใช้เป็นสะพานเชื่อม
        $formGroup = DB::table('form_groups')->where('form_group_id', $record->form_group_id)->first();

        // 3. ข้อมูลพาหนะ, บริษัท, Supply และ พนักงานตรวจ
        $vehicle = DB::table('vehicles_detail')
            ->leftJoin('vehicle_types', 'vehicles_detail.car_type', '=', 'vehicle_types.id')
            ->leftJoin('insurance_companies', 'vehicles_detail.car_insurance_id', '=', 'insurance_companies.id')
            ->where('vehicles_detail.car_id', $record->veh_id)
            ->select('vehicles_detail.*', 'vehicle_types.vehicle_type', 'insurance_companies.name as insurance_company_name')
            ->first();

        $company = DB::table('company_details')->where('company_id', $vehicle->company_code)->first();
        $supply = DB::table('supply_datas')->where('sup_id', $vehicle->supply_id)->first();
        $form = DB::table('forms')->where('form_id', $record->form_id)->first();
        $inspector = DB::table('users')->where('user_id', $record->user_id)->first();

        // 4. ดึง Pre-inspection Fields (ข้อมูลที่ช่างกรอกก่อนตรวจ)
        $preInspectFields = DB::table('pre_inspection_results')
            ->join('pre_inspection_fields', 'pre_inspection_results.field_id', '=', 'pre_inspection_fields.id')
            ->where('pre_inspection_results.record_id', $record_id)
            ->where('pre_inspection_fields.template_id', $formGroup->pre_inspection_template_id)
            ->get();

        $preImages = DB::table('vehicle_image_records')->where('record_id', $record_id)->first();

        // 5. ดึง Checklist Categories
        $categories = DB::table('check_categories')
            ->where('form_id', $formGroup->check_item_form_id)
            ->orderBy('cates_no', 'asc')
            ->get();

        $results = DB::table('check_records_result')
            ->join('check_items', 'check_records_result.item_id', '=', 'check_items.item_id')
            ->where('check_records_result.record_id', $record_id)
            ->get()
            ->groupBy('category_id');

        $itemImages = DB::table('check_result_images')->where('record_id', $record_id)->get()->groupBy('item_id');

        // 6. ดึง Report Template และ Report Template Fields
        $reportTemplate = DB::table('report_templates')->where('id', $formGroup->report_template_id)->first();
        $reportFields = DB::table('report_template_fields')->where('template_id', $formGroup->report_template_id)->get();

        $checked = '<strong style="color: #000; font-size: 18px;">&#9745;</strong>';
        $unchecked = '<span style="font-size: 18px; color: #666;">&#9744;</span>';

        $replacements = [
            '[car_plate]'            => $vehicle->car_plate ?? '-',
            '[car_trailer_plate]'    => $record->trailer_plate ?? '-',
            '[car_insure]'           => $vehicle->insurance_company_name ?? ($vehicle->car_insure ?? '-'),
            '[car_insurance_expire]' => $vehicle->car_insurance_expire ? thai_date($vehicle->car_insurance_expire) : '-',
            '[car_tax]'              => $vehicle->car_tax ? thai_date($vehicle->car_tax) : '-',
            '[car_register_date]'    => $vehicle->car_register_date ? thai_date($vehicle->car_register_date) : '-',
            '[car_total_weight]'     => $vehicle->car_total_weight ?? '-',
            '[car_fuel]'             => $vehicle->car_fuel_type ?? '-',
            '[car_mileage]'          => $vehicle->car_mileage ?? '-',

            '[company_name]'         => $company->company_name ?? '-',
            '[logo_company]'         => ($company && $company->company_logo) ? '<img src="' . asset($company->company_logo) . '" style="max-height:50px;">' : '',
            '[supply_name]'          => $supply->supply_name ?? '-',

            '[form_code]'            => $form->form_code ?? '-',
            '[form_name]'            => $form->form_name ?? '-',

            '[inspect_date]'         => thai_date($record->inspect_date ?? $record->created_at),
            '[next_inspect_date]'    => $record->next_inspect_date ? thai_date($record->next_inspect_date) : '-',

            '[inspector_name]'       => $inspector ? ($inspector->prefix . $inspector->name . ' ' . $inspector->lastname) : '-',
            '[inspector_sign]'       => $record->inspector_sign ? '<img src="' . asset($record->inspector_sign) . '" style="max-height:50px;">' : '',
             '[driver_sign]'       => $record->driver_sign ? '<img src="' . asset($record->driver_sign) . '" style="max-height:50px;">' : '',

            '[check_status_1]'       => ($record->evaluate_status == 1) ? $checked : $unchecked,
            '[check_status_2]'       => ($record->evaluate_status == 2) ? $checked : $unchecked,
            '[check_status_3]'       => ($record->evaluate_status == 3) ? $checked : $unchecked,
        ];

        // 6.2 นำ Tag จาก report_template_fields (เช่น [driver_name]) มาจับคู่กับข้อมูลที่กรอกไว้
        if ($reportFields->isNotEmpty()) {
            foreach ($reportFields as $rField) {
                $tag = '[' . $rField->field_key . ']';

                $matchedField = $preInspectFields->firstWhere('field_label', $rField->field_label);

                if ($matchedField) {
                    $replacements[$tag] = $matchedField->field_value;
                } elseif (!isset($replacements[$tag])) {
                    // ถ้าไม่เจอ และยังไม่มีใน Tag พื้นฐาน ให้แสดงเป็นค่าว่าง หรือ '-'
                    $replacements[$tag] = '-';
                }
            }
        }


        // 6.3 ประมวลผลลบ Tag ออกและใส่ข้อมูลจริงลงใน HTML
        if ($reportTemplate) {
            $reportTemplate->header_html = str_replace(array_keys($replacements), array_values($replacements), $reportTemplate->header_html);
            if (!empty($reportTemplate->footer_html)) {
                $reportTemplate->footer_html = str_replace(array_keys($replacements), array_values($replacements), $reportTemplate->footer_html);
            }
        }

        // ==========================================
        // Email submission data: collect inspection dates per round
        // in the same 15-day cycle as the current record
        // ==========================================
        $cycleRecords = collect();
        if ($record->chk_status == '1') {
            // Find cycle anchor (round-1 record of this cycle)
            $cycleAnchor = DB::table('chk_records')
                ->where('veh_id', $record->veh_id)
                ->where('chk_status', '1')
                ->where('id', '<=', $record->id)
                ->orderBy('id', 'desc')
                ->get();

            // Walk back to find the cycle's first record (within 15-day window)
            $anchor = null;
            foreach ($cycleAnchor as $r) {
                if ($anchor === null) {
                    $anchor = $r;
                    continue;
                }
                $deadline = \Carbon\Carbon::parse($r->updated_at)->copy()->addDays(15);
                if (\Carbon\Carbon::parse($anchor->updated_at)->lte($deadline)) {
                    $anchor = $r; // still in same cycle, keep walking back
                } else {
                    break; // out of window, stop
                }
            }

            // Get all finalized records from cycle anchor onwards (max 3)
            if ($anchor) {
                $cycleRecords = DB::table('chk_records')
                    ->where('veh_id', $record->veh_id)
                    ->where('chk_status', '1')
                    ->where('id', '>=', $anchor->id)
                    ->orderBy('id', 'asc')
                    ->limit(3)
                    ->get();
            }
        }

        // Map round dates: round1 / round2 / round3 — prefer inspect_date if set
        $r1 = $cycleRecords->get(0);
        $r2 = $cycleRecords->get(1);
        $r3 = $cycleRecords->get(2);
        $roundDates = [
            'round1' => $r1 ? ($r1->inspect_date ?? $r1->updated_at) : null,
            'round2' => $r2 ? ($r2->inspect_date ?? $r2->updated_at) : null,
            'round3' => $r3 ? ($r3->inspect_date ?? $r3->updated_at) : null,
        ];

        // Find the "passed" date (latest record with evaluate_status = 1)
        $passedRecord = $cycleRecords->firstWhere('evaluate_status', '1');
        $passedDate = $passedRecord ? ($passedRecord->inspect_date ?? $passedRecord->updated_at) : null;

        // Final evaluation summary text
        $evalStatus = (string) ($record->evaluate_status ?? '');
        $evaluateText = match ($evalStatus) {
            '1'     => 'ผ่าน',
            '2'     => 'ไม่ผ่าน',
            '3'     => 'ไม่ผ่าน',
            default => '-',
        };

        $vehicleDocs = DB::table('vehicle_documents')
            ->where('veh_id', $record->veh_id)
            ->where('is_active', 1)
            ->where('file_extension', 'pdf')
            ->orderBy('created_at', 'asc')
            ->get();

        $preInspectDocs = DB::table('pre_inspection_results')
            ->join('pre_inspection_fields', 'pre_inspection_results.field_id', '=', 'pre_inspection_fields.id')
            ->where('pre_inspection_results.record_id', $record->record_id)
            ->where('pre_inspection_fields.field_type', 'document')
            ->select(
                'pre_inspection_fields.field_label as doc_name',
                'pre_inspection_results.field_value as file_path',
                'pre_inspection_results.created_at'
            )
            ->orderBy('pre_inspection_results.created_at', 'asc')
            ->get();

        $preInspectPhotos = DB::table('pre_inspection_results')
            ->join('pre_inspection_fields', 'pre_inspection_results.field_id', '=', 'pre_inspection_fields.id')
            ->where('pre_inspection_results.record_id', $record->record_id)
            ->where('pre_inspection_fields.field_type', 'image')
            ->select(
                'pre_inspection_fields.field_label as photo_label',
                'pre_inspection_results.field_value as file_path',
                'pre_inspection_fields.sort_order'
            )
            ->orderBy('pre_inspection_fields.sort_order', 'asc')
            ->get();

        return view('pages.inspection.report', compact(
            'record',
            'supply',
            'vehicle',
            'formGroup',
            'preInspectFields',
            'preImages',
            'categories',
            'results',
            'itemImages',
            'reportTemplate',
            'roundDates',
            'passedDate',
            'evaluateText',
            'vehicleDocs',
            'preInspectDocs',
            'preInspectPhotos'
        ));
    }

    /**
     * Get the first record of the current 15-day inspection cycle for a vehicle.
     * Returns null if no active cycle exists (no records, or last cycle expired).
     *
     * Logic: The cycle "first" is the earliest finalized record. If today is
     * past 15 days from that record's updated_at, the cycle has expired and
     * we treat it as no active cycle (allowing fresh inspection).
     */
    private function getCycleFirstRecord($car_id)
    {
        $first = DB::table('chk_records')
            ->where('veh_id', $car_id)
            ->where('chk_status', '1') // Only finalized records count toward cycle
            ->orderBy('id', 'asc')
            ->first();

        if (!$first) {
            return null;
        }

        // Check if cycle has expired (>15 days from first finalized record)
        $firstFinalized = \Carbon\Carbon::parse($first->updated_at);
        $deadline       = $firstFinalized->copy()->addDays(15);

        if (\Carbon\Carbon::now()->gt($deadline)) {
            return null;
        }

        return $first;
    }

    /**
     * Calculate the round number (1, 2, or 3) for a NEW inspection.
     * Counts finalized records in the active cycle, plus 1 for the new round.
     */
    private function getCurrentRoundNumber($car_id)
    {
        $first = $this->getCycleFirstRecord($car_id);

        if (!$first) {
            return 1; // Fresh inspection
        }

        $count = DB::table('chk_records')
            ->where('veh_id', $car_id)
            ->where('id', '>=', $first->id)
            ->where('chk_status', '1')
            ->count();

        return $count + 1;
    }


    private function getRetestContext($car_id, $current_record_id)
    {
        // Get the cycle's first record
        $first = $this->getCycleFirstRecord($car_id);
        if (!$first) {
            return null; // No active cycle = round 1
        }

        // Get the latest finalized record IN THE CYCLE that's BEFORE the current record
        $previousRecord = DB::table('chk_records')
            ->where('veh_id', $car_id)
            ->where('id', '>=', $first->id)
            ->where('chk_status', '1')
            ->where('record_id', '!=', $current_record_id)
            ->orderBy('id', 'desc')
            ->first();

        if (!$previousRecord) {
            return null; // No previous record = round 1
        }

        // Get all results from the previous record
        $previousResults = DB::table('check_records_result')
            ->where('record_id', $previousRecord->record_id)
            ->get()
            ->keyBy('item_id');

        $retestIds = [];
        $passedIds = [];

        foreach ($previousResults as $itemId => $res) {
            // Status 0 = fail, 2 = almost = need retest
            // Status 1 = pass = auto-passed in this round (readonly)
            if (in_array($res->result_status, ['0', '2'])) {
                $retestIds[] = $itemId;
            } elseif ($res->result_status === '1') {
                $passedIds[] = $itemId;
            }
        }

        return [
            'retest_item_ids'        => $retestIds,
            'passed_item_ids'        => $passedIds,
            'previous_record_id'     => $previousRecord->record_id,
            'previous_round_results' => $previousResults,
        ];
    }

    /**
     * Get active draft information for a vehicle (if any).
     * A draft is considered "active" if:
     *   - chk_status is 0 or 2 (incomplete or saved-as-draft)
     *   - created_at is within 15 days (drafts older than that are abandoned)
     *
     * Returns: ['record_id' => ..., 'inspector_name' => ...] or null
     */
    private function getActiveDraft($car_id)
    {
        $draft = DB::table('chk_records')
            ->leftJoin('users', 'chk_records.user_id', '=', 'users.user_id')
            ->where('chk_records.veh_id', $car_id)
            ->whereIn('chk_records.chk_status', ['0', '2'])
            ->orderBy('chk_records.id', 'desc')
            ->select(
                'chk_records.record_id',
                'chk_records.user_id as draft_user_id',
                'chk_records.created_at as draft_created_at',
                'users.name as inspector_name'
            )
            ->first();

        if (!$draft) {
            return null;
        }

        // Check if draft is older than 15 days = considered abandoned
        $draftCreated = \Carbon\Carbon::parse($draft->draft_created_at);
        if (\Carbon\Carbon::now()->diffInDays($draftCreated) > 15) {
            return null;
        }

        return [
            'record_id'       => $draft->record_id,
            'user_id'         => $draft->draft_user_id,
            'inspector_name'  => $draft->inspector_name ?? 'ผู้ใช้',
        ];
    }

    /**
     * Check if a vehicle is allowed to be inspected (eligibility check).
     *
     * Returns:
     *   ['status' => 'allowed', 'reason' => null]
     *   ['status' => 'locked',  'reason' => 'message', 'inspector_name' => 'name']
     *   ['status' => 'blocked', 'reason' => 'message']
     */
    private function checkInspectionEligibility($car_id)
    {
        // 1. Check for active draft (lock by another inspector)
        $activeDraft = $this->getActiveDraft($car_id);
        if ($activeDraft) {
            return [
                'status'         => 'locked',
                'reason'         => 'อยู่ระหว่างการตรวจโดย ' . $activeDraft['inspector_name'],
                'inspector_name' => $activeDraft['inspector_name'],
            ];
        }

        // 2. Get latest finalized record
        $latest = DB::table('chk_records')
            ->where('veh_id', $car_id)
            ->where('chk_status', '1')
            ->orderBy('id', 'desc')
            ->first();

        // No finalized record yet = always allowed (round 1)
        if (!$latest) {
            return ['status' => 'allowed', 'reason' => null];
        }

        // 3. Block if last evaluation passed
        if ($latest->evaluate_status == 1) {
            return [
                'status' => 'blocked',
                'reason' => 'รถคันนี้ตรวจผ่านแล้ว',
            ];
        }

        // 4. Cycle-based round check
        $first = $this->getCycleFirstRecord($car_id);

        // Cycle expired = treat as fresh inspection
        if (!$first) {
            return ['status' => 'allowed', 'reason' => null];
        }

        // Count rounds in current cycle
        $roundCount = DB::table('chk_records')
            ->where('veh_id', $car_id)
            ->where('id', '>=', $first->id)
            ->where('chk_status', '1')
            ->count();

        if ($roundCount >= 3) {
            return [
                'status' => 'blocked',
                'reason' => 'รถคันนี้ได้รับการตรวจครบ 3 ครั้งในรอบนี้แล้ว',
            ];
        }

        return ['status' => 'allowed', 'reason' => null];
    }
}
