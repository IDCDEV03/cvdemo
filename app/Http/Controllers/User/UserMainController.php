<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Enums\Role;
use Illuminate\Support\Facades\File;
use App\Services\CheckRecordNotifier;

class UserMainController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:user']);
    }

    public function profile()
    {
        $user = Auth::user();
        return view('pages.user.UserProfile', compact('user'));
    }

    public function announce()
    {
        $list_post = DB::table('announcements')
            ->join('users', 'announcements.user_id', '=', 'users.id')
            ->select('users.name', 'users.role', 'users.id', 'announcements.id as post_id', 'announcements.title', 'announcements.description', 'announcements.file_upload', 'announcements.updated_at', 'announcements.created_at')
            ->orderBy('announcements.updated_at', 'DESC')
            ->get();

        return view('pages.local.announce', compact('list_post'));
    }

    public function veh_edit($id)
    {
        $vehicle = DB::table('vehicles_detail')
            ->join('vehicle_types', 'vehicles_detail.car_type', '=', 'vehicle_types.id')
            ->select('vehicles_detail.*', 'vehicle_types.vehicle_type as veh_type_name')
            ->where('vehicles_detail.car_id', '=', $id)
            ->first();

        return view('pages.user.VehiclesEdit', ['id' => $id], compact('vehicle'));
    }

    public function veh_update(Request $request)
    {

        $car_id = $request->car_id;
        $user_id = Auth::user()->user_id;

        DB::table('vehicles_detail')->where('car_id', $car_id)->update([
            'car_brand' => $request->car_brand,
            'car_model' => $request->car_model,
            'car_number_record' => $request->car_number_record,
            'car_age' => $request->car_age,
            'updated_at' => Carbon::now(),
        ]);


        DB::table('vehicles_update_log')->insert([
            'user_update_id' => $user_id,
            'car_id' => $car_id,
            'update_remark' => 'user_update_data',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return redirect()->route('local.home')->with('success', 'อัปเดตข้อมูลรถสำเร็จ');
    }


    public function veh_regis()
    {

        $car_type = DB::table('vehicle_types')
            ->select('id', 'vehicle_type')
            ->orderBy('id', 'ASC')
            ->get();

        $province = DB::table('provinces')
            ->select('id', 'name_th')
            ->orderBy('name_th', 'ASC')
            ->get();

        $car_brand = DB::table('car_brands')
            ->orderBy('brand_name', 'ASC')
            ->get();

        return view('pages.user.VehiclesRegister', compact('car_type', 'province', 'car_brand'));
    }

    public function veh_detail($id)
    {

        $user = Auth::user()->user_id;

        $vehicle = DB::table('vehicles_detail')
            ->join('vehicle_types', 'vehicles_detail.car_type', '=', 'vehicle_types.id')
            ->select('vehicles_detail.*', 'vehicle_types.vehicle_type as veh_type_name')
            ->where('vehicles_detail.car_id', '=', $id)
            ->first();

        $record = DB::table('chk_records')
            ->join('forms', 'forms.form_id', '=', 'chk_records.form_id')
            ->select('chk_records.created_at as date_check', 'chk_records.form_id', 'chk_records.record_id', 'forms.form_name', 'chk_records.veh_id')
            ->orderBy('chk_records.created_at', 'DESC')
            ->where('chk_records.user_id', $user)
            ->where('chk_records.veh_id', $id)
            ->get();

        return view('pages.user.VehiclesDetail', ['id' => $id], compact('vehicle', 'record'));
    }


    public function veh_insert(Request $request)
    {

        $agent = DB::table('users')->where('id', Auth::id())->first();

        if (empty($request->province)) {
            return redirect()->back()->with('error', 'กรุณาเลือกทะเบียนจังหวัด');
        }

        if (empty($request->veh_brand)) {
            return redirect()->back()->with('error', 'กรุณาเลือกยี่ห้อรถ');
        }

        $veh_id = 'VEH-' . Str::upper(Str::random(9));

        $rawInput = $request->input('plate');
        $cleanPlate = str_replace(' ', '', $rawInput); //ตัดช่องว่างออก

        // upload image
        $upload_location = 'upload/';
        $file = $request->file('vehicle_image');
        $extension = $file->getClientOriginalExtension();
        $newName = $cleanPlate . '_' . Carbon::now()->format('Ymd_His') . '.' . $extension;
        $file->move($upload_location, $newName);
        $fileName = $upload_location . $newName;

        DB::table('vehicles')->insert([
            'veh_id' => $veh_id,
            'veh_brand' => $request->veh_brand,
            'plate' => $cleanPlate,
            'province' => $request->province,
            'user_create_id' => $agent->user_id,
            'veh_status' => '1',
            'veh_type' => $request->vehicle_type,
            'veh_image' => $fileName,
            'agency_id' => $agent->agency_id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return redirect()->route('local.home')->with('success', 'บันทึกสำเร็จ');
    }

    public function chk_list(Request $request)
    {
        $user_id = Auth::user()->user_id;
        $dateFrom = $request->input('date_from');
        $dateTo   = $request->input('date_to');

        $record = DB::table('chk_records')
            ->join('vehicles_detail', 'chk_records.veh_id', '=', 'vehicles_detail.car_id')
            ->join('vehicle_types', 'vehicles_detail.car_type', '=', 'vehicle_types.id')
            ->select(
                'vehicles_detail.*',
                'vehicle_types.vehicle_type as veh_type_name',
                'chk_records.created_at as date_check',
                'chk_records.form_id',
                'chk_records.record_id',
                'chk_records.user_id as chk_user',
                'chk_records.agency_id as chk_agent'
            )
            ->where('chk_records.user_id', '=', $user_id)
            ->orderBy('chk_records.created_at', 'DESC');            

        if ($dateFrom && $dateTo) {
            $record->whereBetween('chk_records.created_at', [
                Carbon::parse($dateFrom)->startOfDay(),
                Carbon::parse($dateTo)->endOfDay(),
            ]);
        } elseif ($dateFrom) {
            $record->where('chk_records.created_at', '>=', Carbon::parse($dateFrom)->startOfDay());
        } elseif ($dateTo) {
            $record->where('chk_records.created_at', '<=', Carbon::parse($dateTo)->endOfDay());
        }

        $record_all = $record->get();

        return view('pages.user.ChkList', compact('record_all'));
    }

    public function start_check($id)
    {
        $forms = DB::table('forms')
            ->where('form_status', '=', '1')
            ->orderBy('form_name', 'ASC')
            ->get();

        $veh_detail = DB::table('vehicles_detail')
            ->select('car_plate')
            ->where('car_id', $id)
            ->first();

        return view('pages.user.ChkStart', compact('forms', 'veh_detail'));
    }

    public function insert_new1(Request $request, $id)
    {

        $user_main_id = Auth::user()->user_id;

        $user_sup = DB::table('inspector_datas')
            ->where('ins_id', $user_main_id)
            ->first();

        $company = DB::table('supply_datas')
            ->where('sup_id', $user_sup->sup_id)
            ->first();

        $validated = $request->validate([
            'image1' => 'nullable|image|mimes:jpg,jpeg,png|max:3048',
            'image2' => 'nullable|image|mimes:jpg,jpeg,png|max:3048',
            'image3' => 'nullable|image|mimes:jpg,jpeg,png|max:3048',
            'image4' => 'nullable|image|mimes:jpg,jpeg,png|max:3048',
            'image5' => 'nullable|image|mimes:jpg,jpeg,png|max:3048',
            'image6' => 'nullable|image|mimes:jpg,jpeg,png|max:3048',
            'image7' => 'nullable|image|mimes:jpg,jpeg,png|max:3048',
            'image8' => 'nullable|image|mimes:jpg,jpeg,png|max:3048',
        ]);

        if (empty($request->form_id)) {
            return redirect()->back()->with('error', 'กรุณาเลือกฟอร์มที่ต้องการใช้');
        }

        $record_id = 'REC-' . Str::upper(Str::random(10));

        $upload_location = 'upload/vehicle_images/';

        $data = [
            'user_create_id' => $user_main_id,
            'company_id' => $company->company_code,
            'supply_id' => $user_sup->sup_id,
            'record_id' => $record_id,
            'veh_id' => $id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];

        for ($i = 1; $i <= 8; $i++) {
            $field = "image{$i}";
            if ($request->hasFile($field)) {
                $file = $request->file($field);

                $filename = $id . '_' . "{$field}_" . date('Ymd') . '_' . Str::random(5) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('upload/vehicle_images'), $filename);

                $data[$field] = $filename;
            } else {
                $data[$field] = null;
            }
        }

        DB::table('chk_records')->insert([
            'user_id' =>  $user_main_id,
            'veh_id' => $id,
            'record_id' => $record_id,
            'form_id' => $request->form_id,
            'agency_id' => $user_sup->sup_id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('vehicle_image_records')->insert($data);

        $firstCategory = DB::table('check_categories')
            ->where('form_id', $request->form_id)
            ->orderBy('cates_no')
            ->first();

        return redirect()->route('user.chk_step2', ['rec' => $record_id, 'cats' => $firstCategory->category_id]);
    }



    public function insert_step1(Request $request, $id)
    {

        $user_main_id = Auth::id();
        $user_gen_id = DB::table('users')
            ->where('id', '=', $user_main_id)
            ->first();

        $user_gen = $user_gen_id->user_id;

        $user_sup = DB::table('inspector_datas')
            ->where('ins_id', $user_gen)
            ->first();

        if (empty($request->form_id)) {
            return redirect()->back()->with('error', 'กรุณาเลือกฟอร์มที่ต้องการใช้');
        }

        $record_id = 'REC-' . Str::upper(Str::random(10));


        $upload_location = 'upload/';

        $frontPath = $request->file('front_image');
        $extension = $frontPath->getClientOriginalExtension();
        $newName_front = $id . '_' . 'front' . '_' . Carbon::now()->format('Ymd') . '.' . $extension;
        $frontPath->move($upload_location, $newName_front);
        $fileName_front = $upload_location . $newName_front;

        $sidePath = $request->file('side_image');
        $extension2 = $sidePath->getClientOriginalExtension();
        $newName_side = $id . '_' . 'side' . '_' . Carbon::now()->format('Ymd') . '.' . $extension2;
        $sidePath->move($upload_location, $newName_side);
        $fileName_side = $upload_location . $newName_side;

        $overallPath = $request->file('overall_image');
        $extension3 = $overallPath->getClientOriginalExtension();
        $newName_overall = $id . '_' . 'overall' . '_' . Carbon::now()->format('Ymd') . '.' . $extension3;
        $overallPath->move($upload_location, $newName_overall);
        $fileName_overall = $upload_location . $newName_overall;


        DB::table('chk_records')->insert([
            'user_id' => $user_gen,
            'veh_id' => $id,
            'record_id' => $record_id,
            'form_id' => $request->form_id,
            'agency_id' => $user_sup->sup_id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $firstCategory = DB::table('check_categories')
            ->where('form_id', $request->form_id)
            ->orderBy('cates_no')
            ->first();

        return redirect()->route('user.chk_step2', ['rec' => $record_id, 'cats' => $firstCategory->category_id]);
    }

    public function chk_step2($rec, $cats)
    {
        $forms = DB::table('check_categories')
            ->join('forms', 'check_categories.form_id', '=', 'forms.form_id')
            ->select('forms.form_name', 'forms.form_id')
            ->where('check_categories.category_id', '=', $cats)
            ->first();

        $record = DB::table('chk_records')->where('record_id', $rec)->first();

        $category = DB::table('check_categories')->where('category_id', $cats)->first();

        // ✅ ดึง items พร้อมผลตรวจของ record นี้ (LEFT JOIN)
        $items = DB::table('check_items as i')
            ->leftJoin('check_records_result as r', function ($join) use ($rec) {
                $join->on('r.item_id', '=', 'i.id')   // ถ้า PK ของ check_items เป็น 'id' ให้เปลี่ยนเป็น 'i.id'
                    ->where('r.record_id', '=', $rec);
            })
            ->where('i.category_id', $category->category_id)
            ->orderBy('i.item_no')
            ->select('i.*', 'r.result_value', 'r.user_comment')
            ->get();



        $checkedCategories = DB::table('check_records_result')
            ->join('check_items', 'check_records_result.item_id', '=', 'check_items.id')
            ->where('check_records_result.record_id', $rec)
            ->pluck('check_items.category_id')
            ->unique()   // กันซ้ำ
            ->toArray();

        $allCategories = DB::table('check_categories')
            ->where('form_id', $category->form_id)
            ->orderBy('cates_no', 'asc') // ถ้ามีลำดับหมวด
            ->get();



        return view('pages.user.ChkStep2', compact('record', 'category', 'items', 'forms', 'allCategories', 'checkedCategories'));
    }

    public function chk_insert_step2(Request $request, $record_id, $category_id)
    {

        foreach ($request->item_result as $item_id => $value) {
            DB::table('check_records_result')->insert([
                'user_id' => Auth::id(),
                'record_id' => $record_id,
                'item_id' => $item_id,
                'result_value' => $value,
                'user_comment' => $request->user_comment[$item_id] ?? null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            if ($request->hasFile("item_images.$item_id")) {
                foreach ($request->file("item_images.$item_id") as $image) {
                    $imagePath = 'upload/';
                    $newname = $item_id . '_' . $image->getClientOriginalName();
                    $image->move($imagePath, $newname);
                    $fileName = $imagePath . $newname;

                    DB::table('check_result_images')->insert([
                        'record_id' => $record_id,
                        'item_id' => $item_id,
                        'image_path' => $fileName,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                }
            }
        }

        //หมวดถัดไป
        $current = DB::table('check_categories')->where('category_id', $category_id)->first();
        $next = DB::table('check_categories')
            ->where('form_id', $current->form_id)
            ->where('cates_no', '>', $current->cates_no)
            ->orderBy('cates_no')
            ->first();

        if ($next) {
            return redirect()->route('user.chk_step2', ['rec' => $record_id, 'cats' => $next->category_id]);
        }
        return redirect()->route('form_report', ['rec' => $record_id])->with('success', 'บันทึกสำเร็จ');
    }

    //* สำหรับบันทึกไม่เรียงหมวดหมู่ *//
    public function storeOrUpdate(Request $request, $recordId, $categoryId)
    {
        foreach ($request->input('item_result', []) as $item_id => $value) {
            $comment = $request->input("user_comment.$item_id");

            if ($request->hasFile("item_images.$item_id")) {
                foreach ($request->file("item_images.$item_id") as $image) {
                    $imagePath = 'upload/';
                    $newname = $item_id . '_' . $image->getClientOriginalName();
                    $image->move($imagePath, $newname);
                    $fileName = $imagePath . $newname;

                    DB::table('check_result_images')->insert([
                        'record_id' => $recordId,
                        'item_id' => $item_id,
                        'image_path' => $fileName,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                }
            }

            DB::table('check_records_result')->updateOrInsert(
                [
                    'user_id' => Auth::id(),
                    'record_id'   => $recordId,
                    'item_id'     => $item_id,
                ],
                [
                    'result_value' => $value,
                    'user_comment' => $comment,
                    'updated_at'   => now(),
                    'created_at'   => now(),
                ]
            );
        }

        // หา "หมวดถัดไป" ตามลำดับ cates_no
        $currentCat = DB::table('check_categories')->where('category_id', $categoryId)->first();

        $nextCatId = DB::table('check_categories')
            ->where('form_id', $currentCat->form_id)
            ->where('cates_no', '>', $currentCat->cates_no)
            ->orderBy('cates_no')
            ->value('category_id');

        if ($nextCatId) {
            // ไปหมวดถัดไป
            return redirect()
                ->route('user.chk_step2', ['rec' => $recordId, 'cats' => $nextCatId])
                ->with('success', 'บันทึกผลการตรวจเรียบร้อยแล้ว');
        }

        // ถ้าเป็นหมวดสุดท้าย → ไปสรุป
        return redirect()
            ->route('user.chk_summary', $recordId)
            ->with('success', 'บันทึกครบทุกหมวดแล้ว กำลังไปหน้ารายงานผล');
    }

    //*หน้าสรุปผล*//
    public function summary($recordId)
    {
        $record = DB::table('chk_records')->where('record_id', $recordId)->first();

        // ถ้า confirm แล้ว → redirect กลับ
        if ($record->chk_status === '1') {
            return redirect()->route('records.category', [$recordId, 1])
                ->with('error', 'การตรวจนี้ถูกยืนยันแล้ว ไม่สามารถแก้ไขได้');
        }

        $veh_detail = DB::table('vehicles_detail')
            ->where('car_id', $record->veh_id)
            ->first();

        $categories = DB::table('check_categories')
            ->where('form_id', $record->form_id)
            ->orderBy('cates_no')
            ->get();

        $items = DB::table('check_items')
            ->select('id', 'category_id', 'item_name', 'item_no')
            ->whereIn('category_id', $categories->pluck('category_id'))
            ->orderBy('category_id', 'asc')   // ให้เรียงตามหมวดก่อน
            ->orderBy('item_no', 'asc')
            ->get();

        $itemsByCategory = $items->groupBy('category_id');

        // ดึงผลการตรวจทั้งหมดของ record นี้ map ด้วย item_id
        $results = DB::table('check_records_result')
            ->where('record_id', $recordId)
            ->get()
            ->keyBy('item_id');

        $checkedCategories = DB::table('check_records_result')
            ->join('check_items', 'check_records_result.item_id', '=', 'check_items.id')
            ->where('check_records_result.record_id', $recordId)
            ->pluck('check_items.category_id')
            ->unique()   // กันซ้ำ
            ->toArray();

        $images = DB::table('check_result_images')
            ->where('record_id', $recordId)
            ->select('item_id', 'image_path')
            ->orderBy('id', 'asc')
            ->get()
            ->groupBy('item_id');

        $imageBase = '/';


        // คำนวณ progress
        $totalItems   = $items->count();
        $checkedItems = $results->count();
        $progress     = $totalItems > 0 ? round(($checkedItems / $totalItems) * 100) : 0;



        return view('pages.user.Summary', compact('record', 'categories', 'itemsByCategory', 'results', 'veh_detail', 'totalItems', 'checkedItems', 'progress', 'checkedCategories', 'images', 'imageBase'));
    }


    public function chk_result($record_id)
    {
        $record = DB::table('chk_records')
            ->join('vehicles_detail', 'chk_records.veh_id', '=', 'vehicles_detail.car_id')
            ->join('vehicle_types', 'vehicles_detail.car_type', '=', 'vehicle_types.id')
            ->select('vehicles_detail.*', 'vehicle_types.vehicle_type as veh_type_name', 'chk_records.created_at as date_check', 'chk_records.form_id', 'chk_records.record_id', 'chk_records.user_id as chk_user', 'chk_records.agency_id as chk_agent')
            ->where('chk_records.record_id', $record_id)->first();

        $agent_name = DB::table('users')
            ->where('user_id', $record->chk_agent)
            ->first();

        $forms = DB::table('forms')
            ->select('forms.form_name')
            ->where('form_id', '=', $record->form_id)
            ->first();

        $categories = DB::table('check_categories')
            ->where('form_id', $record->form_id)
            ->orderBy('cates_no')
            ->get();

        $results = DB::table('check_records_result')
            ->join('check_items', 'check_records_result.item_id', '=', 'check_items.id')
            ->where('record_id', $record_id)
            ->select('check_items.category_id', 'check_items.item_name', 'check_records_result.item_id', 'result_value', 'user_comment')
            ->get()
            ->groupBy('category_id');

        $images = DB::table('check_result_images')
            ->where('record_id', $record_id)
            ->get()
            ->groupBy('item_id');

        $item_chk = DB::table('check_records_result')
            ->select('record_id', 'item_id', DB::raw('COUNT(result_value) as count'))
            ->where('record_id', $record_id)
            ->whereIn('result_value', [0, 2])
            ->groupBy('record_id', 'item_id')
            ->get();

        return view('pages.user.ChkResult', compact('agent_name', 'record', 'results', 'forms', 'categories', 'images', 'item_chk'));
    }

    public function confirm($record, CheckRecordNotifier $notifier)
    {

        DB::transaction(function () use ($record, $notifier) {

        $rec = DB::table('chk_records')
            ->where('record_id', $record)
            ->lockForUpdate()
            ->first();

        if (!$rec) {
            abort(404, 'ไม่พบข้อมูลการตรวจ');
        }

        if ($rec->chk_status === '1') {
            return;
        }
        
        DB::table('chk_records')
            ->where('id', $rec->id)
            ->update([
                'chk_status' => '1',
                'updated_at' => now(),
            ]);

        $updatedRec = DB::table('chk_records')->where('id', $rec->id)->first();
        
        $notifier->notifySupplyOnConfirmed((array) $updatedRec);
    });

    return redirect()
        ->route('user.chk_list')
        ->with('success', 'ยืนยันผลตรวจเรียบร้อย');
    }

    public function chk_search(Request $request)
    {
        $hasSearch = $request->boolean('searched');

        $checks = null;
        if ($hasSearch) {
            $dateFrom = $request->input('date_from');
            $dateTo   = $request->input('date_to');

            $q = DB::table('chk_records as r')
                ->join('vehicles_detail as v', 'v.car_id', '=', 'r.veh_id')
                ->select('r.record_id', 'r.updated_at', 'v.car_plate', 'v.car_brand', 'v.car_model', 'v.var_number_record')
                ->orderByDesc('r.updated_at');

            if ($dateFrom && $dateTo) {
                $q->whereBetween('r.updated_at', [
                    Carbon::parse($dateFrom)->startOfDay(),
                    Carbon::parse($dateTo)->endOfDay(),
                ]);
            } elseif ($dateFrom) {
                $q->where('r.updated_at', '>=', Carbon::parse($dateFrom)->startOfDay());
            } elseif ($dateTo) {
                $q->where('r.updated_at', '<=', Carbon::parse($dateTo)->endOfDay());
            }

            $checks = $q->paginate(25);
            $checks->appends($request->query());
        }

        return view('pages.user.ChkList', compact('checks', 'hasSearch'));
    }
}
