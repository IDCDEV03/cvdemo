<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Enums\Role;

class VehicleController extends Controller
{

// ============================================================
// INDEX — render page + KPI counts
// ============================================================
public function index()
{
    $user     = Auth::user();
    $userRole = $user->role->value;

    // --- Dropdown: vehicle types ---
    $vehicleTypes = DB::table('vehicle_types')
        ->select('id', 'vehicle_type')
        ->orderBy('id', 'asc')
        ->get();

    $companies          = collect();
    $supplies           = collect();
    $inspector          = null;
    $showCompanyFilter  = false;
    $showSupplyFilter   = false;

    if ($userRole === 'company') {
        $showSupplyFilter = true;
        $supplies = DB::table('vehicles_detail as v')
            ->join('supply_datas as s', 'v.supply_id', '=', 's.sup_id')
            ->where('v.company_code', $user->company_code)
            ->whereNotNull('s.sup_id')
            ->select('s.sup_id', 's.supply_name')
            ->distinct()
            ->orderBy('s.supply_name')
            ->get();

    } elseif ($userRole === 'inspector') {
        $inspector = DB::table('inspector_datas')->where('ins_id', $user->user_id)->first();
        if ($inspector && $inspector->inspector_type == '3') {
            $showSupplyFilter = true;
            $allowedIds = DB::table('inspector_supply_access')
                ->where('ins_id', $user->user_id)
                ->pluck('supply_id')
                ->toArray();
            $supplies = DB::table('supply_datas')
                ->whereIn('sup_id', $allowedIds)
                ->select('sup_id', 'supply_name')
                ->orderBy('supply_name')
                ->get();
        }

    } elseif ($userRole === 'supply') {
        // supply role has no extra filter dropdowns — scoped by their own sup_id

    } else {
        // staff / admin / manager
        $showCompanyFilter = true;
        $showSupplyFilter  = true;
        $companies = DB::table('company_details')
            ->select('company_id', 'company_name')
            ->where('require_user_approval', '1')
            ->orderBy('company_name')
            ->get();
        $supplies = DB::table('supply_datas')
            ->select('sup_id', 'supply_name')
            ->orderBy('supply_name')
            ->get();
    }

    // --- Summary: yearly inspection result ---
    $currentYear   = now()->year;
    $currentYearBE = $currentYear + 543;

    $summaryQuery = DB::table('chk_records as cr')
        ->join('vehicles_detail as v', 'v.car_id', '=', 'cr.veh_id')
        ->whereYear('cr.created_at', $currentYear)
        ->where('cr.chk_status', 1);

    if ($userRole === 'company') {
        $summaryQuery->where('v.company_code', $user->company_code);
    } elseif ($userRole === 'supply') {
        $summaryQuery->where('v.supply_id', $user->user_id);
    } elseif ($userRole === 'inspector' && $inspector) {
        if ($inspector->inspector_type == '1') {
            $summaryQuery->where('v.company_code', $inspector->company_code);
        } elseif ($inspector->inspector_type == '2') {
            $summaryQuery->where('v.supply_id', $inspector->sup_id);
        } elseif ($inspector->inspector_type == '3') {
            $summaryQuery->whereIn('v.supply_id', $allowedIds ?? []);
        }
    }

    $summary = $summaryQuery->selectRaw("
        COUNT(DISTINCT cr.veh_id) as total_inspected,
        SUM(CASE WHEN cr.evaluate_status = 1 THEN 1 ELSE 0 END) as passed,
        SUM(CASE WHEN cr.evaluate_status IN (2,3) THEN 1 ELSE 0 END) as failed
    ")->first();

    return view('pages.vehicles.index', compact(
        'userRole',
        'vehicleTypes',
        'companies',
        'supplies',
        'summary',
        'currentYearBE',
        'showCompanyFilter',
        'showSupplyFilter'
    ));
}

// ============================================================
// AJAX INDEX — server-side DataTable
// GET /staff/vehicles/ajax
// Params: draw, start, length, search[value],
//         order[0][column], order[0][dir],
//         filter_status, filter_inspect, filter_company, filter_type
// ============================================================
public function ajaxIndex(Request $request)
{
    $user     = Auth::user();
    $userRole = $user->role->value;

    // --- Base query ---
    $query = DB::table('vehicles_detail as v')
        ->leftJoin('supply_datas as s', 's.sup_id', '=', 'v.supply_id')
        ->leftJoin('vehicle_types as vt', 'vt.id', '=', 'v.car_type')
        ->leftJoinSub(
            DB::table('chk_records')
                ->select('veh_id', DB::raw('MAX(id) as max_id'))
                ->where('chk_status', 1)
                ->groupBy('veh_id'),
            'li',
            'li.veh_id', '=', 'v.car_id'
        )
        ->leftJoin('chk_records as cr', 'cr.id', '=', 'li.max_id')
        ->select(
            'v.car_id',
            'v.car_plate',
            'vt.vehicle_type as vehicle_type_name',
            DB::raw('COALESCE(cr.inspect_date, cr.created_at) as inspect_date'),
            'cr.evaluate_status'
        );

    // --- Auto-filter by role ---
    $inspectorAllowedSupplyIds = [];
    if ($userRole === 'company') {
        $query->where('v.company_code', $user->company_code);
    } elseif ($userRole === 'supply') {
        $query->where('v.supply_id', $user->user_id);
    } elseif ($userRole === 'inspector') {
        $inspector = DB::table('inspector_datas')->where('ins_id', $user->user_id)->first();
        if ($inspector) {
            if ($inspector->inspector_type == '1') {
                if (empty($inspector->company_code)) {
                    return response()->json(['draw' => intval($request->draw), 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => []]);
                }
                $query->where('v.company_code', $inspector->company_code);
            } elseif ($inspector->inspector_type == '2') {
                if (empty($inspector->sup_id)) {
                    return response()->json(['draw' => intval($request->draw), 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => []]);
                }
                $query->where('v.supply_id', $inspector->sup_id);
            } elseif ($inspector->inspector_type == '3') {
                $inspectorAllowedSupplyIds = DB::table('inspector_supply_access')
                    ->where('ins_id', $user->user_id)
                    ->pluck('supply_id')
                    ->toArray();
                if (empty($inspectorAllowedSupplyIds)) {
                    return response()->json([
                        'draw'            => intval($request->draw),
                        'recordsTotal'    => 0,
                        'recordsFiltered' => 0,
                        'data'            => [],
                    ]);
                }
                $query->whereIn('v.supply_id', $inspectorAllowedSupplyIds);
            }
        }
    }

    // --- Filter: inspection result ---
    if ($request->filled('filter_inspect') && $request->filter_inspect !== 'all') {
        if ($request->filter_inspect === '0') {
            $query->whereNull('cr.id');
        } else {
            $query->where('cr.evaluate_status', $request->filter_inspect);
        }
    }

    // --- Filter: company (staff/admin/manager only) ---
    if (in_array($userRole, ['staff', 'admin', 'manager']) && $request->filled('filter_company')) {
        $query->where('v.company_code', $request->filter_company);
    }

    // --- Filter: supply (company / inspector type3 / staff) ---
    if ($request->filled('filter_supply')) {
        if ($userRole === 'company' || in_array($userRole, ['staff', 'admin', 'manager'])) {
            $query->where('v.supply_id', $request->filter_supply);
        } elseif ($userRole === 'inspector' && in_array($request->filter_supply, $inspectorAllowedSupplyIds)) {
            $query->where('v.supply_id', $request->filter_supply);
        }
    }

    // --- Filter: vehicle type ---
    if ($request->filled('filter_type')) {
        $query->where('v.car_type', $request->filter_type);
    }

    // --- Search: plate (ทุก role) ---
    if ($request->filled('search_plate')) {
        $query->where('v.car_plate', 'like', '%' . $request->search_plate . '%');
    }

    // --- Search: text box (staff only) ---
    if ($request->filled('search_text')) {
        $keyword = $request->search_text;
        $query->where(function ($q) use ($keyword) {
            $q->where('v.car_plate', 'like', "%{$keyword}%")
              ->orWhere('s.supply_name', 'like', "%{$keyword}%");
        });
    }

    // --- Total records (after filter) ---
    $recordsFiltered = $query->count();

    // --- Total records (no filter, scoped by role) ---
    if ($userRole === 'company') {
        $recordsTotal = DB::table('vehicles_detail')->where('company_code', $user->company_code)->count();
    } elseif ($userRole === 'supply') {
        $recordsTotal = DB::table('vehicles_detail')->where('supply_id', $user->user_id)->count();
    } elseif ($userRole === 'inspector') {
        $ins = DB::table('inspector_datas')->where('ins_id', $user->user_id)->first();
        if (!$ins) {
            $recordsTotal = 0;
        } elseif ($ins->inspector_type == '1') {
            $recordsTotal = DB::table('vehicles_detail')->where('company_code', $ins->company_code)->count();
        } elseif ($ins->inspector_type == '2') {
            $recordsTotal = DB::table('vehicles_detail')->where('supply_id', $ins->sup_id)->count();
        } elseif ($ins->inspector_type == '3') {
            $ids = DB::table('inspector_supply_access')->where('ins_id', $user->user_id)->pluck('supply_id')->toArray();
            $recordsTotal = DB::table('vehicles_detail')->whereIn('supply_id', $ids)->count();
        } else {
            $recordsTotal = 0;
        }
    } else {
        $recordsTotal = DB::table('vehicles_detail')->count();
    }

    // --- Ordering ---
    $colMap = [
        0 => 'v.car_plate',
        1 => 'vt.vehicle_type',
        2 => DB::raw('COALESCE(cr.inspect_date, cr.created_at)'),
    ];
    $orderCol = $colMap[$request->input('order.0.column', 2)] ?? DB::raw('COALESCE(cr.inspect_date, cr.created_at)');
    $orderDir = $request->input('order.0.dir', 'desc') === 'desc' ? 'desc' : 'asc';
    $query->orderBy($orderCol, $orderDir);

    // --- Pagination ---
    $start  = (int) $request->input('start', 0);
    $length = (int) $request->input('length', 25);
    $rows   = $query->offset($start)->limit($length)->get();

    // --- Format rows ---
    $data = $rows->map(function ($row) use ($userRole) {
        $inspBadge = match ((string) $row->evaluate_status) {
            '1' => '<span class="text-success fw-bold fs-16">ผ่าน</span>',
            '2' => '<span class="text-warning fw-bold fs-16">ไม่ปกติ แต่ใช้งานได้</span>',
            '3' => '<span class="text-danger fw-bold fs-16">ไม่ปกติ ห้ามใช้งาน</span>',
            default => '<span class="text-muted fs-16">-</span>',
        };

        $inspDate = $row->inspect_date ? thai_date($row->inspect_date) : '<span class="text-muted">ไม่พบข้อมูล</span>';

        $actions = '<div class="d-flex gap-1">
            <a href="' . route('vehicles.show', $row->car_id) . '"
               class="btn btn-sm btn-transparent-primary py-0 px-2" title="ดูรายละเอียด">
                รายละเอียด
            </a>';
        if ($userRole !== 'company') {
            $actions .= '<a href="' . route('vehicles.edit', $row->car_id) . '"
               class="btn btn-sm btn-transparent-secondary py-0 px-2" title="แก้ไข">
                แก้ไข
            </a>';
        }
        $actions .= '</div>';

        return [
            $row->car_plate,
            $row->vehicle_type_name ?? '-',
            $inspDate,
            $inspBadge,
            $actions,
        ];
    });

    return response()->json([
        'draw'            => (int) $request->input('draw'),
        'recordsTotal'    => $recordsTotal,
        'recordsFiltered' => $recordsFiltered,
        'data'            => $data,
    ]);
}

    // ============================================
    // CREATE - Show 3-step register form
    // ============================================
    public function create()
    {
        $user     = Auth::user();
        $userRole = $user->role->value;

        // Load companies filtered by inspector type
        if ($userRole === 'inspector') {
            $inspector = DB::table('inspector_datas')
                ->where('ins_id', $user->user_id)
                ->first();

            if (!$inspector) {
                return redirect()->route('vehicles.index')
                    ->with('error', 'ไม่พบข้อมูลผู้ตรวจ');
            }

            switch ($inspector->inspector_type) {
                case '1':
                    $companies = DB::table('company_details')
                        ->where('company_id', $inspector->company_code)
                        ->select('company_id', 'company_name')
                        ->orderBy('company_name')
                        ->get();
                    break;

                case '2':
                    $supply = DB::table('supply_datas')
                        ->where('sup_id', $inspector->sup_id)
                        ->first();
                    $companies = $supply
                        ? DB::table('company_details')
                            ->where('company_id', $supply->company_code)
                            ->select('company_id', 'company_name')
                            ->get()
                        : collect();
                    break;

                case '3':
                    $allowedSupIds = DB::table('inspector_supply_access')
                        ->where('ins_id', $inspector->ins_id)
                        ->pluck('supply_id');
                    $companyIds = DB::table('supply_datas')
                        ->whereIn('sup_id', $allowedSupIds)
                        ->pluck('company_code')
                        ->unique()
                        ->values();
                    $companies = DB::table('company_details')
                        ->whereIn('company_id', $companyIds)
                        ->select('company_id', 'company_name')
                        ->orderBy('company_name')
                        ->get();
                    break;

                default:
                    return redirect()->route('vehicles.index')
                        ->with('error', 'ประเภทผู้ตรวจไม่ถูกต้อง');
            }
        } else {
            $companies = DB::table('company_details')
                ->select('company_id', 'company_name')
                ->orderBy('company_name')
                ->get();
        }

        // Load static masters
        $car_brands = DB::table('car_brands')
            ->orderBy('brand_name')
            ->get();

        $vehicle_types = DB::table('vehicle_types')
            ->orderBy('vehicle_type')
            ->get();

        $provinces = DB::table('provinces')
            ->orderBy('name_th')
            ->get();

        $insuranceCompanies = DB::table('insurance_companies')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('pages.vehicles.create', compact(
            'companies',
            'car_brands',
            'vehicle_types',
            'provinces',
            'insuranceCompanies'
        ));
    }

    // ============================================
    // STORE - Save new vehicle
    // ============================================
    // ============================================
    // STORE - Save new vehicle (with documents + log)
    // ============================================
    public function store(Request $request)
    {
        // ============================================
        // 1. Validation
        // ============================================
        $validator = Validator::make($request->all(), [
            // Step 1-2
            'company_id'    => 'required|string|exists:company_details,company_id',
            'supply_id'     => 'required|string|exists:supply_datas,sup_id',

            // Step 3: required fields
            'plate'         => 'required|string|max:20',
            'province'      => 'required|string|max:50',
            'car_brand'     => 'required|string|max:50',
            'car_type'      => 'required|integer|exists:vehicle_types,id',
            'car_model'     => 'required|string|max:50',

            // Optional fields
            'car_number_record'    => 'nullable|string|max:50',
            'car_age'              => 'nullable|string|max:20',
            'car_mileage'          => 'nullable|string|max:20',
            'car_trailer_plate'    => 'nullable|string|max:200',
            'car_fuel_type'        => 'nullable|string|max:200',
            'car_weight'           => 'nullable|string|max:100',
            'car_total_weight'     => 'nullable|string|max:200',
            'car_product'          => 'nullable|string|max:100',
            'car_insurance_id'     => 'nullable|integer|exists:insurance_companies,id',

            // Date fields (already converted to Y-m-d by JS helper)
            'car_tax'              => 'nullable|date',
            'car_register_date'    => 'nullable|date',
            'car_insurance_start'  => 'nullable|date',
            'car_insurance_expire' => 'nullable|date',

            // Status (0 or 1)
            'status'        => 'required|in:0,1',

            // Image (5 MB max)
            'vehicle_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',

            // Document (10 MB max, PDF or DOCX)
            'vehicle_document' => 'nullable|file|mimes:pdf,docx|max:10240',
            'doc_name'         => 'nullable|string|max:200',
        ], [
            'plate.required'        => 'กรุณากรอกทะเบียนรถ',
            'province.required'     => 'กรุณาเลือกจังหวัดทะเบียน',
            'car_brand.required'    => 'กรุณาเลือกยี่ห้อรถ',
            'car_type.required'     => 'กรุณาเลือกประเภทรถ',
            'car_type.exists'       => 'ประเภทรถไม่ถูกต้อง',
            'car_model.required'    => 'กรุณากรอกรุ่นรถ',
            'vehicle_image.image'   => 'ไฟล์ภาพต้องเป็นรูปภาพเท่านั้น',
            'vehicle_image.max'     => 'ไฟล์ภาพต้องไม่เกิน 5 MB',
            'vehicle_document.mimes' => 'เอกสารต้องเป็น PDF หรือ DOCX เท่านั้น',
            'vehicle_document.max'  => 'เอกสารต้องไม่เกิน 10 MB',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // ============================================
        // 2. Server-side guard: Check supply limit
        // ============================================
        $supply = DB::table('supply_datas')
            ->where('sup_id', $request->supply_id)
            ->first();

        if (!$supply) {
            return redirect()->back()
                ->with('error', 'ไม่พบข้อมูล Supply')
                ->withInput();
        }

        // ============================================
        // 3. Server-side guard: Inspector scope check
        // ============================================
        $user = Auth::user();
        if ($user->role->value === 'inspector') {
            $inspector = DB::table('inspector_datas')
                ->where('ins_id', $user->user_id)
                ->first();

            if (!$inspector) {
                return redirect()->back()
                    ->with('error', 'ไม่พบข้อมูลผู้ตรวจ')
                    ->withInput();
            }

            $allowed = false;
            switch ($inspector->inspector_type) {
                case '1':
                    $allowed = ($request->company_id === $inspector->company_code);
                    break;
                case '2':
                    $allowed = ($request->supply_id === $inspector->sup_id)
                        && ($supply->company_code === $inspector->company_code);
                    break;
                case '3':
                    $allowed = DB::table('inspector_supply_access')
                        ->where('ins_id', $inspector->ins_id)
                        ->where('supply_id', $request->supply_id)
                        ->exists();
                    break;
            }

            if (!$allowed) {
                return redirect()->back()
                    ->with('error', 'คุณไม่มีสิทธิ์ลงทะเบียนรถภายใต้ Supply นี้')
                    ->withInput();
            }
        }

        // ============================================
        // 4. Server-side guard: Check plate uniqueness within supply
        // ============================================
        $cleanPlate = str_replace(' ', '', trim($request->plate));
        $car_plate = $cleanPlate . ' ' . $request->province;

        $plateExists = DB::table('vehicles_detail')
            ->where('supply_id', $request->supply_id)
            ->where('car_plate', $car_plate)
            ->exists();

        if ($plateExists) {
            return redirect()->back()
                ->with('error', 'ทะเบียนรถนี้มีอยู่ใน Supply นี้แล้ว')
                ->withInput();
        }

        // ============================================
        // 5. Generate car_id
        // ============================================
        $veh_id = $this->generateUniqueVehId();

        // ============================================
        // 6. DB Transaction
        // ============================================
        DB::beginTransaction();

        try {
            $user = Auth::user();
            $now = now();

            // 6.1 Prepare image path (will save after insert)
            $imagePath = null;
            if ($request->hasFile('vehicle_image')) {
                $imagePath = $this->saveVehicleImage($request->file('vehicle_image'), $veh_id);
            }

            // 6.2 Prepare data for vehicles_detail
            $vehicleData = [
                'user_id'              => $user->user_id,
                'company_code'         => $request->company_id,
                'supply_id'            => $request->supply_id,
                'car_id'               => $veh_id,
                'car_plate'            => $car_plate,
                'car_brand'            => $request->car_brand,
                'car_model'            => $request->car_model,
                'car_number_record'    => $request->car_number_record,
                'car_age'              => $request->car_age,
                'car_tax'              => $request->car_tax,
                'car_mileage'          => $request->car_mileage,
                'car_insurance_id'     => $request->car_insurance_id ?: null,
                'car_insurance_start'  => $request->car_insurance_start ?: null,
                'car_type'             => $request->car_type,
                'car_image'            => $imagePath,
                'status'               => $request->status,
                'car_trailer_plate'    => $request->car_trailer_plate,
                'car_register_date'    => $request->car_register_date,
                'car_insurance_expire' => $request->car_insurance_expire,
                'car_weight'           => $request->car_weight,
                'car_total_weight'     => $request->car_total_weight,
                'car_fuel_type'        => $request->car_fuel_type,
                'car_product'          => $request->car_product,
                'created_at'           => $now,
                'updated_at'           => $now,
            ];

            // 6.3 Insert vehicle and get ID
            $vehicleId = DB::table('vehicles_detail')->insertGetId($vehicleData);

            // 6.4 Upload document (if any)
            if ($request->hasFile('vehicle_document')) {
                $docName = $request->doc_name ?: ('เอกสารประจำปี ' . (now()->year + 543));
                $this->saveVehicleDocument(
                    $request->file('vehicle_document'),
                    $veh_id,
                    $docName,
                    $user->user_id
                );
            }

            // 6.5 Insert activity log
            DB::table('vehicle_activity_logs')->insert([
                'vehicle_id'  => $vehicleId,
                'user_id'     => $user->user_id,
                'action'      => 'create',
                'before_data' => null,
                'after_data'  => json_encode($vehicleData, JSON_UNESCAPED_UNICODE),
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);

            DB::commit();

            // ============================================
            // 7. Redirect with success
            // ============================================
            return redirect()->route('vehicles.show', $veh_id)
                ->with('success', 'ลงทะเบียนรถเรียบร้อยแล้ว!');
        } catch (\Exception $e) {
            DB::rollBack();

            // Cleanup uploaded files if transaction failed
            if (!empty($imagePath) && file_exists(public_path($imagePath))) {
                @unlink(public_path($imagePath));
            }

            return redirect()->back()
                ->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage())
                ->withInput();
        }
    }

    // ============================================
    // HELPER: Generate unique veh_id
    // ============================================
    private function generateUniqueVehId()
    {
        do {
            $veh_id = 'VEH-' . Str::upper(Str::random(9));
            $exists = DB::table('vehicles_detail')->where('car_id', $veh_id)->exists();
        } while ($exists);

        return $veh_id;
    }

    // ============================================
    // HELPER: Save vehicle image
    // ============================================
    private function saveVehicleImage($file, $veh_id)
    {
        // Build folder path
        $folder = 'uploads/vehicles/' . $veh_id;
        $absolutePath = public_path($folder);

        // Create folder if not exists
        if (!file_exists($absolutePath)) {
            mkdir($absolutePath, 0755, true);
        }

        // Generate unique filename
        $extension = $file->getClientOriginalExtension();
        $filename = time() . '_' . Str::random(8) . '.' . $extension;

        // Move file
        $file->move($absolutePath, $filename);

        // Return relative path (for DB)
        return $folder . '/' . $filename;
    }

    // ============================================
    // HELPER: Save vehicle document
    // ============================================
    // ============================================
    // HELPER: Save vehicle document
    // (Fixed: get file size BEFORE move)
    // ============================================
    private function saveVehicleDocument($file, $veh_id, $docName, $uploadedBy)
    {
        // Build folder path
        $folder = 'uploads/vehicle_docs/' . $veh_id;
        $absolutePath = public_path($folder);

        // Create folder if not exists
        if (!file_exists($absolutePath)) {
            mkdir($absolutePath, 0755, true);
        }

        // ✅ Get file info BEFORE move (temp file still exists)
        $extension    = strtolower($file->getClientOriginalExtension());
        $originalName = $file->getClientOriginalName();
        $fileSize     = $file->getSize(); // ← ย้ายมาก่อน move()

        // Generate unique filename
        $filename = time() . '_' . Str::random(8) . '.' . $extension;

        // Move file (temp file will be deleted after this)
        $file->move($absolutePath, $filename);

        $relativePath = $folder . '/' . $filename;

        // Insert into vehicle_documents
        DB::table('vehicle_documents')->insert([
            'veh_id'             => $veh_id,
            'doc_name'           => $docName,
            'file_path'          => $relativePath,
            'file_original_name' => $originalName,
            'file_extension'     => $extension,
            'file_size'          => $fileSize, // ← ใช้ค่าที่เก็บไว้แล้ว
            'uploaded_by'        => $uploadedBy,
            'is_active'          => 1,
            'remark'             => null,
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        return $relativePath;
    }

    // ============================================
    // SHOW - Vehicle detail page
    // ============================================
    public function show($veh_id)
    {
        // Main vehicle data with joins
        $vehicle = DB::table('vehicles_detail as v')
            ->leftJoin('supply_datas as s', 'v.supply_id', '=', 's.sup_id')
            ->leftJoin('company_details as c', 'v.company_code', '=', 'c.company_id')
            ->leftJoin('vehicle_types as vt', 'v.car_type', '=', DB::raw('CAST(vt.id AS CHAR)'))
            ->leftJoin('insurance_companies as ic', 'v.car_insurance_id', '=', 'ic.id')
            ->where('v.car_id', $veh_id)
            ->select(
                'v.*',
                's.supply_name',
                'c.company_name',
                'vt.vehicle_type as vehicle_type_name',
                'ic.name as insurance_company_name'
            )
            ->first();

        if (!$vehicle) {
            return redirect()->route('vehicles.index')
                ->with('error', 'ไม่พบข้อมูลรถ');
        }

        // Active document (latest)
        $activeDocument = DB::table('vehicle_documents')
            ->where('veh_id', $veh_id)
            ->where('is_active', 1)
            ->orderBy('created_at', 'desc')
            ->first();

        // Archived documents (history)
        $documentHistory = DB::table('vehicle_documents as d')
            ->leftJoin('users as u', 'd.uploaded_by', '=', 'u.user_id')
            ->where('d.veh_id', $veh_id)
            ->where('d.is_active', 0)
            ->select(
                'd.*',
                DB::raw("CONCAT(u.prefix, u.name, ' ', u.lastname) as uploader_name")
            )
            ->orderBy('d.created_at', 'desc')
            ->get();

        // Active document uploader name
        $activeDocUploader = null;
        if ($activeDocument) {
            $activeDocUploader = DB::table('users')
                ->where('user_id', $activeDocument->uploaded_by)
                ->selectRaw("CONCAT(prefix, name, ' ', lastname) as full_name")
                ->value('full_name');
        }

        // Last inspection date — prefer inspect_date, fallback to updated_at
        $lastInspectDate = DB::table('chk_records')
            ->where('veh_id', $veh_id)
            ->where('chk_status', 1)
            ->max(DB::raw('COALESCE(inspect_date, updated_at)'));

        //  inspection record
        $inspectionRecords = DB::table('chk_records as r')
            ->leftJoin('inspector_datas as i', 'r.user_id', '=', 'i.ins_id')
            ->where('r.veh_id', $veh_id)
            ->select(
                'r.record_id',
                DB::raw('COALESCE(r.inspect_date, r.created_at) as inspect_date'),
                'r.evaluate_status',
                'r.next_inspect_date',
                'r.chk_status',
                DB::raw("CONCAT(i.ins_prefix, i.ins_name, ' ', i.ins_lastname) as inspector_name")
            )
            ->orderBy('r.created_at', 'desc')
            ->get();

        // Supply change history
        $supplyHistory = DB::table('vehicle_supply_history')
            ->where('car_id', $veh_id)
            ->orderBy('changed_at', 'desc')
            ->get();

        return view('pages.vehicles.show', compact(
            'vehicle',
            'activeDocument',
            'activeDocUploader',
            'documentHistory',
            'inspectionRecords',
            'lastInspectDate',
            'supplyHistory'
        ));
    }

    // ============================================
    // AJAX: Change vehicle status
    // ============================================
    public function changeStatus(Request $request, $veh_id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:0,1,2',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'สถานะไม่ถูกต้อง']);
        }

        $vehicle = DB::table('vehicles_detail')
            ->where('car_id', $veh_id)
            ->first();

        if (!$vehicle) {
            return response()->json(['success' => false, 'message' => 'ไม่พบข้อมูลรถ']);
        }

        DB::table('vehicles_detail')
            ->where('car_id', $veh_id)
            ->update([
                'status'     => $request->status,
                'updated_at' => now(),
            ]);

        // Log status change
        DB::table('vehicle_activity_logs')->insert([
            'vehicle_id'  => $vehicle->id,
            'user_id'     => Auth::user()->user_id,
            'action'      => 'status_change',
            'before_data' => json_encode(['status' => $vehicle->status]),
            'after_data'  => json_encode(['status' => $request->status]),
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        $statusLabels = ['0' => 'ปิดการใช้งาน', '1' => 'เปิดการใช้งาน', '2' => 'ห้ามใช้งาน'];

        return response()->json([
            'success' => true,
            'message' => 'เปลี่ยนสถานะเป็น "' . $statusLabels[$request->status] . '" เรียบร้อย',
            'new_status' => $request->status,
        ]);
    }
    // ============================================
    // EDIT - Show edit form
    // ============================================

public function edit($veh_id)
{
    // --- Fetch vehicle with joins ---
    $vehicle = DB::table('vehicles_detail as v')
        ->leftJoin('company_details as c', 'c.company_id', '=', 'v.company_code')
        ->leftJoin('supply_datas as s', 's.sup_id', '=', 'v.supply_id')
        ->leftJoin('vehicle_types as vt', 'vt.id', '=', 'v.car_type')
        ->select(
            'v.*',
            'c.company_name',
            's.supply_name',
            'vt.vehicle_type as vehicle_type_name'
        )
        ->where('v.car_id', $veh_id)
        ->first();

    if (!$vehicle) {
        return redirect()->route('vehicles.index')
            ->with('error', 'ไม่พบข้อมูลรถ');
    }

    // --- Supplies under same company (for step 2 dropdown) ---
    $supplies = DB::table('supply_datas')
        ->where('company_code', $vehicle->company_code)
        ->where('supply_status', '1')
        ->orderBy('supply_name')
        ->get();

    // --- Current supply info (for limit display) ---
    $currentSupply = DB::table('supply_datas')
        ->where('sup_id', $vehicle->supply_id)
        ->first();

    $currentVehicleCount = DB::table('vehicles_detail')
        ->where('supply_id', $vehicle->supply_id)
        ->count();

    // --- Dropdown data ---
    $provinces          = DB::table('provinces')->orderBy('name_th')->get();
    $car_brands         = DB::table('car_brands')->orderBy('brand_name')->get();
    $vehicle_types      = DB::table('vehicle_types')->orderBy('vehicle_type')->get();
    $insuranceCompanies = DB::table('insurance_companies')
        ->where('is_active', true)
        ->orderBy('name')
        ->get();

    // --- Split plate ---
    $plateParts    = $vehicle->car_plate ? explode(' ', $vehicle->car_plate, 2) : ['', ''];
    $plateOnly     = $plateParts[0] ?? '';
    $plateProvince = $plateParts[1] ?? '';

    return view('pages.vehicles.edit', compact(
        'vehicle',
        'supplies',
        'currentSupply',
        'currentVehicleCount',
        'provinces',
        'car_brands',
        'vehicle_types',
        'plateOnly',
        'plateProvince',
        'insuranceCompanies'
    ));
}

// ============================================================
// UPDATE — validate + save changes
// PUT /staff/vehicles/{veh_id}
// ============================================================
public function update(Request $request, $veh_id)
{
    // --- Fetch existing vehicle ---
    $vehicle = DB::table('vehicles_detail')
        ->where('car_id', $veh_id)
        ->first();

    if (!$vehicle) {
        return redirect()->route('vehicles.index')
            ->with('error', 'ไม่พบข้อมูลรถ');
    }

    // --- Validate ---
    $request->validate([
        'supply_id'            => ['required', 'string'],
        'plate'                => ['required', 'string', 'max:20'],
        'province'             => ['required', 'string'],
        'car_brand'            => ['required', 'string'],
        'car_type'             => ['required'],
        'car_model'            => ['nullable', 'string', 'max:100'],
        'car_number_record'    => ['nullable', 'string', 'max:50'],
        'car_age'              => ['nullable', 'string', 'max:10'],
        'car_mileage'          => ['nullable', 'numeric'],
        'car_trailer_plate'    => ['nullable', 'string', 'max:20'],
        'car_fuel_type'        => ['nullable', 'string', 'max:50'],
        'car_weight'           => ['nullable', 'numeric'],
        'car_total_weight'     => ['nullable', 'numeric'],
        'car_product'          => ['nullable', 'string', 'max:100'],
        'car_insurance_id'     => ['nullable', 'integer', 'exists:insurance_companies,id'],
        'car_tax'              => ['nullable', 'date'],
        'car_register_date'    => ['nullable', 'date'],
        'car_insurance_start'  => ['nullable', 'date'],
        'car_insurance_expire' => ['nullable', 'date'],
        'status'               => ['required'],
        'vehicle_image'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        'vehicle_document'     => ['nullable', 'file', 'mimes:pdf,docx', 'max:10240'],
        'doc_name'             => ['nullable', 'string', 'max:200'],
    ], [
        'supply_id.required' => 'กรุณาเลือก Supply',
        'plate.required'     => 'กรุณากรอกทะเบียนรถ',
        'province.required'  => 'กรุณาเลือกจังหวัด',
        'car_brand.required' => 'กรุณาเลือกยี่ห้อรถ',
        'car_type.required'  => 'กรุณาเลือกประเภทรถ',
        'car_model.required' => 'กรุณากรอกรุ่นรถ',
    ]);

    // --- Build full plate string ---
    $plate    = trim($request->input('plate'));
    $province = trim($request->input('province'));
    $fullPlate = $plate . ' ' . $province;

    // --- Check duplicate plate (exclude self) ---
    $plateExists = DB::table('vehicles_detail')
        ->where('car_plate', $fullPlate)
        ->where('supply_id', $request->input('supply_id'))
        ->where('car_id', '!=', $veh_id)
        ->exists();

    if ($plateExists) {
        return back()
            ->withInput()
            ->with('error', 'ทะเบียนรถนี้มีอยู่ใน Supply นี้แล้ว');
    }

    // --- Handle supply change: adjust quota counts ---
    $oldSupId = $vehicle->supply_id;
    $newSupId = $request->input('supply_id');
    $supplyChanged = $oldSupId !== $newSupId;

    // supply changed — no quota limit enforced

    // --- Handle vehicle image ---
    $imagePath = $vehicle->car_image; // keep existing by default

    if ($request->hasFile('vehicle_image')) {
        // Delete old image if exists
        if ($imagePath && file_exists(public_path($imagePath))) {
            unlink(public_path($imagePath));
        }
        $imgFile  = $request->file('vehicle_image');
        $imgExt   = $imgFile->getClientOriginalExtension();
        $imgName  = now()->format('YmdHis') . '_' . \Illuminate\Support\Str::random(8) . '.' . $imgExt;
        $imgDir   = public_path("uploads/vehicle_images/{$veh_id}");
        if (!file_exists($imgDir)) {
            mkdir($imgDir, 0775, true);
        }
        $imgFile->move($imgDir, $imgName);
        $imagePath = "uploads/vehicle_images/{$veh_id}/{$imgName}";
    }

    DB::beginTransaction();
    try {
        // --- Update vehicles_detail ---
        DB::table('vehicles_detail')
            ->where('car_id', $veh_id)
            ->update([
                'supply_id'            => $newSupId,
                'car_plate'            => $fullPlate,
                'car_brand'            => $request->input('car_brand'),
                'car_type'             => $request->input('car_type'),
                'car_model'            => $request->input('car_model'),
                'car_number_record'    => $request->input('car_number_record'),
                'car_age'              => $request->input('car_age'),
                'car_mileage'          => $request->input('car_mileage') ?: null,
                'car_trailer_plate'    => $request->input('car_trailer_plate'),
                'car_fuel_type'        => $request->input('car_fuel_type'),
                'car_weight'           => $request->input('car_weight') ?: null,
                'car_total_weight'     => $request->input('car_total_weight') ?: null,
                'car_product'          => $request->input('car_product'),
                'car_insurance_id'     => $request->input('car_insurance_id') ?: null,
                'car_insurance_start'  => $request->input('car_insurance_start') ?: null,
                'car_tax'              => $request->input('car_tax') ?: null,
                'car_register_date'    => $request->input('car_register_date') ?: null,
                'car_insurance_expire' => $request->input('car_insurance_expire') ?: null,
                'status'               => $request->input('status'),
                'car_image'            => $imagePath,
                'updated_at'           => now(),
            ]);

        // --- Log to vehicle_activity_logs ---
        DB::table('vehicle_activity_logs')->insert([
            'vehicle_id'  => DB::table('vehicles_detail')->where('car_id', $veh_id)->value('id'),
            'user_id'     => Auth::user()->user_id,
            'action'      => 'update',
            'before_data' => json_encode($vehicle),
            'after_data'  => json_encode($request->except(['_token', '_method', 'vehicle_image', 'vehicle_document'])),
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        // --- Log supply change to vehicle_supply_history ---
        if ($supplyChanged) {
            $fromSupply = DB::table('supply_datas')->where('sup_id', $oldSupId)->value('supply_name');
            $toSupply   = DB::table('supply_datas')->where('sup_id', $newSupId)->value('supply_name');
            $editor     = Auth::user();
            DB::table('vehicle_supply_history')->insert([
                'car_id'             => $veh_id,
                'from_supply_id'     => $oldSupId,
                'from_supply_name'   => $fromSupply ?? $oldSupId,
                'to_supply_id'       => $newSupId,
                'to_supply_name'     => $toSupply ?? $newSupId,
                'changed_by_user_id' => $editor->user_id,
                'changed_by_name'    => $editor->name,
                'changed_at'         => now(),
            ]);
        }

        // --- Handle new document upload ---
        if ($request->hasFile('vehicle_document')) {
            $docFile  = $request->file('vehicle_document');
            $docExt   = strtolower($docFile->getClientOriginalExtension());
            $docOrig  = $docFile->getClientOriginalName();
            $docSize  = $docFile->getSize();
            $docTs    = now()->format('YmdHis');
            $docRand  = \Illuminate\Support\Str::random(8);
            $docName  = "{$docTs}_{$docRand}.{$docExt}";
            $docDir   = public_path("uploads/vehicle_docs/{$veh_id}");

            if (!file_exists($docDir)) {
                mkdir($docDir, 0775, true);
            }

            $docFile->move($docDir, $docName);
            $docPath = "uploads/vehicle_docs/{$veh_id}/{$docName}";

            // Archive existing active document
            DB::table('vehicle_documents')
                ->where('veh_id', $veh_id)
                ->where('is_active', 1)
                ->update([
                    'is_active'  => 0,
                    'updated_at' => now(),
                ]);

            // Insert new active document
            DB::table('vehicle_documents')->insert([
                'veh_id'             => $veh_id,
                'doc_name'           => $request->input('doc_name') ?: 'เอกสารประจำปี ' . (now()->year + 543),
                'file_path'          => $docPath,
                'file_original_name' => $docOrig,
                'file_extension'     => $docExt,
                'file_size'          => $docSize,
                'uploaded_by'        => Auth::user()->user_id,
                'is_active'          => 1,
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);
        }

        DB::commit();

    } catch (\Exception $e) {
        DB::rollBack();
        return back()
            ->withInput()
            ->with('error', 'บันทึกไม่สำเร็จ: ' . $e->getMessage());
    }

    return redirect()
        ->route('vehicles.show', $veh_id)
        ->with('success', 'แก้ไขข้อมูลรถเรียบร้อยแล้ว');
}
   
    // ============================================
    // DESTROY - Delete vehicle (soft or hard)
    // ============================================
    public function destroy($veh_id)
    {
        // TODO: Part 4 - implement destroy logic
    }

    // ============================================
    // AJAX: Get supplies by company
    // ============================================
    public function getSuppliesByCompany(Request $request)
    {
        $company_id = $request->input('company_id');
        $user       = Auth::user();
        $userRole   = $user->role->value;

        if (empty($company_id)) {
            return response()->json(['success' => false, 'message' => 'company_id required']);
        }

        $query = DB::table('supply_datas')
            ->where('company_code', $company_id)
            ->where('supply_status', '1')
            ->select('sup_id', 'supply_name', 'vehicle_limit')
            ->orderBy('supply_name');

        if ($userRole === 'inspector') {
            $inspector = DB::table('inspector_datas')
                ->where('ins_id', $user->user_id)
                ->first();

            if ($inspector) {
                if ($inspector->inspector_type == '2') {
                    $query->where('sup_id', $inspector->sup_id);
                } elseif ($inspector->inspector_type == '3') {
                    $allowedSupIds = DB::table('inspector_supply_access')
                        ->where('ins_id', $inspector->ins_id)
                        ->pluck('supply_id');
                    $query->whereIn('sup_id', $allowedSupIds);
                }
                // inspector_type = 1: แสดงทุก supply ภายใต้ company
            }
        }

        return response()->json([
            'success' => true,
            'data'    => $query->get(),
        ]);
    }

    // ============================================
    // AJAX: Get supply info (vehicle limit + current count)
    // ============================================
    public function getSupplyInfo($sup_id)
    {
        $supply = DB::table('supply_datas')
            ->where('sup_id', $sup_id)
            ->select('sup_id', 'supply_name', 'vehicle_limit')
            ->first();

        if (!$supply) {
            return response()->json(['success' => false, 'message' => 'Supply not found'], 404);
        }

        // Count current vehicles under this supply
        $current_count = DB::table('vehicles_detail')
            ->where('supply_id', $sup_id)
            ->count();

        $limit = $supply->vehicle_limit ?? 0;
        $remaining = $limit > 0 ? max(0, $limit - $current_count) : null;
        $is_full = ($limit > 0 && $current_count >= $limit);

        return response()->json([
            'success' => true,
            'data' => [
                'sup_id' => $supply->sup_id,
                'supply_name' => $supply->supply_name,
                'vehicle_limit' => $limit,
                'current_count' => $current_count,
                'remaining' => $remaining,
                'is_full' => $is_full,
            ]
        ]);
    }

    // ============================================
    // AJAX: Check car_plate uniqueness within supply
    // ============================================
    public function checkPlateUnique(Request $request)
    {
        $plate = trim($request->input('plate', ''));
        $province = trim($request->input('province', ''));
        $supply_id = $request->input('supply_id');
        $exclude_veh_id = $request->input('exclude_veh_id'); // for edit mode

        if (empty($plate) || empty($supply_id)) {
            return response()->json([
                'success' => false,
                'message' => 'plate and supply_id required'
            ]);
        }

        // Build full plate (same way as store)
        $cleanPlate = str_replace(' ', '', $plate);
        $fullPlate = $cleanPlate . ' ' . $province;

        $query = DB::table('vehicles_detail')
            ->where('supply_id', $supply_id)
            ->where('car_plate', $fullPlate);

        if ($exclude_veh_id) {
            $query->where('car_id', '!=', $exclude_veh_id);
        }

        $exists = $query->exists();

        return response()->json([
            'success' => true,
            'is_unique' => !$exists,
            'message' => $exists
                ? 'ทะเบียนรถนี้มีอยู่ใน Supply นี้แล้ว'
                : 'ทะเบียนนี้ใช้งานได้'
        ]);
    }


    // ============================================================
    // DOCUMENT LIST
    // ============================================================
    public function documentList($veh_id)
    {
        // --- Verify vehicle exists ---
        $vehicle = DB::table('vehicles_detail')
            ->where('car_id', $veh_id)
            ->first();

        if (!$vehicle) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่พบข้อมูลรถ',
            ], 404);
        }

        // --- Active document (current) ---
        $active = DB::table('vehicle_documents')
            ->where('veh_id', $veh_id)
            ->where('is_active', 1)
            ->orderByDesc('created_at')
            ->first();

        // --- Archived documents (history) ---
        $archived = DB::table('vehicle_documents as d')
            ->leftJoin('users as u', 'u.user_id', '=', 'd.uploaded_by')
            ->select(
                'd.*',
                DB::raw("CONCAT(COALESCE(u.first_name,''), ' ', COALESCE(u.last_name,'')) as uploader_name")
            )
            ->where('d.veh_id', $veh_id)
            ->where('d.is_active', 0)
            ->orderByDesc('d.created_at')
            ->get();

        // --- Resolve uploader name for active document ---
        $activeUploaderName = null;
        if ($active) {
            $uploader = DB::table('users')
                ->where('user_id', $active->uploaded_by)
                ->first();
            $activeUploaderName = $uploader
                ? trim(($uploader->first_name ?? '') . ' ' . ($uploader->last_name ?? ''))
                : $active->uploaded_by;
        }

        return response()->json([
            'success'             => true,
            'active'              => $active,
            'active_uploader'     => $activeUploaderName,
            'archived'            => $archived,
            'archived_count'      => $archived->count(),
        ]);
    }

    // ============================================================
    // DOCUMENT UPLOAD
    // POST /staff/vehicles/{veh_id}/documents/upload
    // Archives existing active doc → inserts new active row
    // ============================================================
    public function documentUpload(Request $request, $veh_id)
    {
        // --- Validate ---
        $request->validate([
            'doc_name'         => ['required', 'string', 'max:200'],
            'vehicle_document' => ['required', 'file', 'mimes:pdf,docx', 'max:10240'],
            'remark'           => ['nullable', 'string', 'max:500'],
        ], [
            'doc_name.required'         => 'กรุณาระบุชื่อเอกสาร',
            'vehicle_document.required' => 'กรุณาเลือกไฟล์',
            'vehicle_document.mimes'    => 'รองรับเฉพาะไฟล์ PDF หรือ DOCX เท่านั้น',
            'vehicle_document.max'      => 'ขนาดไฟล์ต้องไม่เกิน 10 MB',
        ]);

        // --- Verify vehicle exists ---
        $vehicle = DB::table('vehicles_detail')
            ->where('car_id', $veh_id)
            ->first();

        if (!$vehicle) {
            return back()->with('error', 'ไม่พบข้อมูลรถ');
        }

        $file      = $request->file('vehicle_document');
        $ext       = strtolower($file->getClientOriginalExtension());
        $original  = $file->getClientOriginalName();
        $size      = $file->getSize();
        $userId    = Auth::user()->user_id;
        $timestamp = now()->format('YmdHis');
        $random    = \Illuminate\Support\Str::random(8);

        // --- Store file: uploads/vehicle_docs/{veh_id}/{timestamp}_{random}.{ext} ---
        $filename  = "{$timestamp}_{$random}.{$ext}";
        $destDir   = public_path("uploads/vehicle_docs/{$veh_id}");
        if (!file_exists($destDir)) {
            mkdir($destDir, 0775, true);
        }
        $file->move($destDir, $filename);
        $path = "uploads/vehicle_docs/{$veh_id}/{$filename}";

        DB::beginTransaction();
        try {
            // --- Archive existing active document ---
            DB::table('vehicle_documents')
                ->where('veh_id', $veh_id)
                ->where('is_active', 1)
                ->update([
                    'is_active'  => 0,
                    'updated_at' => now(),
                ]);

            // --- Insert new active document ---
            $docData = [
                'veh_id'             => $veh_id,
                'doc_name'           => $request->input('doc_name'),
                'file_path'          => $path,
                'file_original_name' => $original,
                'file_extension'     => $ext,
                'file_size'          => $size,
                'uploaded_by'        => $userId,
                'is_active'          => 1,
                'remark'             => $request->input('remark'),
                'created_at'         => now(),
                'updated_at'         => now(),
            ];
            DB::table('vehicle_documents')->insert($docData);

            DB::table('vehicle_activity_logs')->insert([
                'vehicle_id'  => $vehicle->id,
                'user_id'     => $userId,
                'action'      => 'upload_document',
                'before_data' => null,
                'after_data'  => json_encode($docData, JSON_UNESCAPED_UNICODE),
                'created_at'  => now(),
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // Clean up uploaded file if DB failed
            if (file_exists(public_path($path))) {
                unlink(public_path($path));
            }
            return back()->with('error', 'อัปโหลดไม่สำเร็จ: ' . $e->getMessage());
        }

        return back()->with('success', 'อัปโหลดเอกสารสำเร็จ');
    }

    // ============================================================
    // DOCUMENT DOWNLOAD
    // GET /staff/vehicles/{veh_id}/documents/{doc_id}/download
    // Streams file as download response
    // ============================================================
    public function documentDownload($veh_id, $doc_id)
    {
        // --- Fetch document row ---
        $doc = DB::table('vehicle_documents')
            ->where('id', $doc_id)
            ->where('veh_id', $veh_id)
            ->first();

        if (!$doc) {
            abort(404, 'ไม่พบข้อมูลเอกสาร');
        }

        // --- Check file exists on disk ---
        $fullPath = public_path($doc->file_path);
        if (!file_exists($fullPath)) {
            abort(404, 'ไม่พบไฟล์บนเซิร์ฟเวอร์');
        }

        // --- Use original filename for download, fallback to doc_name ---
        $downloadName = $doc->file_original_name
            ?? ($doc->doc_name . '.' . $doc->file_extension);

        return response()->download($fullPath, $downloadName);
    }

    // ============================================================
    // DOCUMENT DELETE
    // DELETE /staff/vehicles/{veh_id}/documents/{doc_id}
    // Deletes file from disk + removes row + logs to vehicle_activity_logs
    // ============================================================
    public function documentDelete($veh_id, $doc_id)
    {
        // --- Fetch document row ---
        $doc = DB::table('vehicle_documents')
            ->where('id', $doc_id)
            ->where('veh_id', $veh_id)
            ->first();

        if (!$doc) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่พบข้อมูลเอกสาร',
            ], 404);
        }

        // --- Verify vehicle exists (needed for activity log FK) ---
        $vehicle = DB::table('vehicles_detail')
            ->where('car_id', $veh_id)
            ->first();

        if (!$vehicle) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่พบข้อมูลรถ',
            ], 404);
        }

        $userId = Auth::user()->user_id;

        DB::beginTransaction();
        try {
            // --- Log before delete (store doc data as before_data) ---
            DB::table('vehicle_activity_logs')->insert([
                'vehicle_id'  => $vehicle->id,       // FK ใช้ vehicles_detail.id (PK)
                'user_id'     => $userId,
                'action'      => 'delete_document',
                'before_data' => json_encode($doc),   // snapshot ก่อนลบ
                'after_data'  => null,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            // --- Delete row from DB ---
            DB::table('vehicle_documents')
                ->where('id', $doc_id)
                ->delete();

            // --- Delete physical file from disk ---
            $fullPath = public_path($doc->file_path);
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'ลบไม่สำเร็จ: ' . $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'ลบเอกสารเรียบร้อยแล้ว',
        ]);
    }
}
