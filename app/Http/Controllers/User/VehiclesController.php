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

class VehiclesController extends Controller
{

  public function veh_list($id)
  {

    $data = DB::table('vehicles')
      ->where('agency_id', '=', $id)
      ->orderBy('id', 'ASC')
      ->get();

    return view('pages.agency.VehiclesList', ['id' => $id], compact('data'));
  }

  public function veh_regis()
  {

    if (!in_array(auth()->user()->role, [Role::User, Role::Staff, Role::Supply, Role::Manager, Role::Agency, Role::Admin])) {
      abort(403);
    }

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

    return view('pages.agency.VehiclesRegister', compact('car_type', 'province', 'car_brand'));
  }

  public function veh_insert(Request $request)
  {

    $agent = Auth::id();

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
      'user_id' => Auth::id(),
      'veh_status' => '1',
      'veh_type' => $request->vehicle_type,
      'veh_image' => $fileName,
      'agency_id' => $agent,
      'created_at' => Carbon::now(),
      'updated_at' => Carbon::now(),
    ]);

    return redirect()->route('agency.veh_list', ['id' => Auth::id()])->with('success', 'บันทึกสำเร็จ');
  }

  public function veh_detail($id)
  {
    $vehicle = DB::table('vehicles_detail')
      ->join('vehicle_types', 'vehicles_detail.car_type', '=', 'vehicle_types.id')
      ->select('vehicles_detail.*', 'vehicle_types.vehicle_type as veh_type_name')
      ->where('vehicles_detail.car_id', '=', $id)
      ->first();

    $record = DB::table('chk_records')
      ->join('forms', 'forms.form_id', '=', 'chk_records.form_id')
      ->select('chk_records.created_at as date_check', 'chk_records.form_id', 'chk_records.record_id', 'forms.form_name', 'chk_records.veh_id')
      ->orderBy('chk_records.created_at', 'DESC')
      ->where('chk_records.veh_id', $id)->get();

    return view('pages.local.VehiclesDetail', ['id' => $id], compact('vehicle', 'record'));
  }

  public function Report_Result($rec)
  {
    // 1. ดึงข้อมูลการตรวจรถ + ยานพาหนะ
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
      ->where('chk_records.record_id', $rec)
      ->first();

    if (!$record) {
      return back()->with('error', 'ไม่พบข้อมูลการตรวจรถที่ต้องการ');
    }

    // 2. ดึงชื่อหน่วยงานที่ตรวจ (agent)
    $agent_name = DB::table('users')
      ->where('user_id', $record->chk_agent)
      ->first();

    // 3. ดึงชื่อแบบฟอร์ม
    $forms = DB::table('forms')
      ->select('form_name')
      ->where('form_id', $record->form_id)
      ->first();

    // 4. ดึงรายการหมวดหมู่ที่เกี่ยวข้องกับแบบฟอร์ม
    $categories = DB::table('check_categories')
      ->where('form_id', $record->form_id)
      ->orderBy('cates_no')
      ->get();

    // 5. ดึงผลการตรวจแยกตามหมวด
    $results = DB::table('check_records_result')
      ->join('check_items', 'check_records_result.item_id', '=', 'check_items.id')
      ->where('record_id', $rec)
      ->select(
        'check_items.category_id',
        'check_items.item_name',
        'check_records_result.item_id',
        'result_value',
        'user_comment'
      )
      ->get()
      ->groupBy('category_id');

    // 6. ดึงภาพที่แนบในการตรวจ
    $images = DB::table('check_result_images')
      ->where('record_id', $rec)
      ->get()
      ->groupBy('item_id');



    return view('pages.local.ReportResult', compact('agent_name', 'record', 'results', 'forms', 'categories', 'images'));
  }

  public function Form_report($rec)
  {
    // 1. ดึงข้อมูลการตรวจรถ + ยานพาหนะ
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
        'chk_records.supply_id as chk_agent'
      )
      ->where('chk_records.record_id', $rec)
      ->first();

    if (!$record) {
      return back()->with('error', 'ไม่พบข้อมูลการตรวจรถที่ต้องการ');
    }

    // 2. ดึงชื่อหน่วยงานที่ตรวจ 
    $agent_name = DB::table('users')
      ->where('user_id', $record->chk_agent)
      ->first();

    //ดึงข้อมูลช่างตรวจ
    $inspector_data = DB::table('inspector_datas')
      ->join('users', 'users.user_id', '=', 'inspector_datas.ins_id')
      ->select(
        'inspector_datas.*',
        'users.user_id'
      )
      ->where('users.user_id', $record->chk_user)
      ->first();


    // 3. ดึงชื่อแบบฟอร์ม
    $forms = DB::table('forms')
      ->select('form_name', 'form_code')
      ->where('form_id', $record->form_id)
      ->first();

    // 4. ดึงรายการหมวดหมู่ที่เกี่ยวข้องกับแบบฟอร์ม
    $exclude_cate10 = ['CAT-10-BAHDYHRW'];

    $categories = DB::table('check_categories')
      ->where('form_id', $record->form_id)
      ->whereNotIN('check_categories.category_id', $exclude_cate10)
      ->orderBy('cates_no')
      ->get();

    //local 
    //$excludeIds = [57, 58, 59, 60, 61];
    //host
    $excludeIds = [42, 43, 44, 45, 46];
    // 5. ดึงผลการตรวจแยกตามหมวด
    $results = DB::table('check_records_result')
      ->join('check_items', 'check_records_result.item_id', '=', 'check_items.id')
      ->where('record_id', $rec)
      ->whereNotIN('check_records_result.item_id', $excludeIds)
      ->select(
        'check_items.category_id',
        'check_items.item_name',
        'check_records_result.item_id',
        'result_value',
        'user_comment'
      )
      ->get()
      ->groupBy('category_id');

    // 6. ดึงภาพที่แนบในการตรวจ
    $images = DB::table('check_result_images')
      ->where('record_id', $rec)
      ->get()
      ->groupBy('item_id');

    $company_datas = DB::table('supply_datas')
      ->join('company_details', 'supply_datas.company_code', '=', 'company_details.company_id')
      ->where('supply_datas.sup_id', $inspector_data->sup_id)
      ->first();

    $rr = DB::table('check_records_result')
      ->where('record_id', $rec)
      ->select('item_id', 'result_value', 'user_comment')
      ->get()
      ->keyBy('item_id');


    return view('pages.local.FormReport', compact('agent_name', 'record', 'results', 'forms', 'categories', 'images', 'inspector_data', 'company_datas', 'rr'));
  }

  public function Form_Image8($rec)
  {

    // 1. ดึงข้อมูลการตรวจรถ + ยานพาหนะ
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
        'chk_records.supply_id as chk_agent'
      )
      ->where('chk_records.record_id', $rec)
      ->first();

    if (!$record) {
      return back()->with('error', 'ไม่พบข้อมูลการตรวจรถที่ต้องการ');
    }

    // 2. ดึงชื่อหน่วยงานที่ตรวจ 
    $agent_name = DB::table('users')
      ->where('user_id', $record->chk_agent)
      ->first();

    //ดึงข้อมูลช่างตรวจ
    $inspector_data = DB::table('inspector_datas')
      ->join('users', 'users.user_id', '=', 'inspector_datas.ins_id')
      ->select(
        'inspector_datas.*',
        'users.user_id'
      )
      ->where('users.user_id', $record->chk_user)
      ->first();


    // 3. ดึงชื่อแบบฟอร์ม
    $forms = DB::table('forms')
      ->select('form_name', 'form_code')
      ->where('form_id', $record->form_id)
      ->first();

    $company_datas = DB::table('supply_datas')
      ->join('company_details', 'supply_datas.company_code', '=', 'company_details.company_id')
      ->where('supply_datas.sup_id', $inspector_data->sup_id)
      ->first();

    //ดึงภาพประเมินรถ
    $image8 = DB::table('vehicle_image_records')
      ->where('record_id', $rec)
      ->first();

    return view('pages.local.FormImage8', compact('agent_name', 'record', 'forms', 'inspector_data', 'company_datas', 'image8'));
  }

  public function FormImage_Fail($rec)
  {

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
        'chk_records.supply_id as chk_agent'
      )
      ->where('chk_records.record_id', $rec)
      ->first();

    if (!$record) {
      return back()->with('error', 'ไม่พบข้อมูลการตรวจรถที่ต้องการ');
    }

    // 2. ดึงชื่อหน่วยงานที่ตรวจ 
    $agent_name = DB::table('users')
      ->where('user_id', $record->chk_agent)
      ->first();

    // 5. ดึงผลการตรวจแยกตามหมวด
    $results = DB::table('check_records_result')
      ->join('check_items', 'check_records_result.item_id', '=', 'check_items.id')
      ->where('record_id', $rec)
      ->whereIn('check_records_result.result_value', ['0', '2'])
      ->select(
        'check_items.category_id',
        'check_items.item_name',
        'check_records_result.item_id',
        'result_value',
        'user_comment'
      )
      ->get()
      ->groupBy('category_id');

    // 4. ดึงรายการหมวดหมู่ที่เกี่ยวข้องกับแบบฟอร์ม
    $categories = DB::table('check_categories')
      ->where('form_id', $record->form_id)
      ->orderBy('cates_no')
      ->get();

    // 6. ดึงภาพที่แนบในการตรวจ
    $images = DB::table('check_result_images')
      ->where('record_id', $rec)
      ->get()
      ->groupBy('item_id');

    //ดึงข้อมูลช่างตรวจ
    $inspector_data = DB::table('inspector_datas')
      ->join('users', 'users.user_id', '=', 'inspector_datas.ins_id')
      ->select(
        'inspector_datas.*',
        'users.user_id'
      )
      ->where('users.user_id', $record->chk_user)
      ->first();


    // 3. ดึงชื่อแบบฟอร์ม
    $forms = DB::table('forms')
      ->select('form_name', 'form_code')
      ->where('form_id', $record->form_id)
      ->first();

    $company_datas = DB::table('supply_datas')
      ->join('company_details', 'supply_datas.company_code', '=', 'company_details.company_id')
      ->where('supply_datas.sup_id', $inspector_data->sup_id)
      ->first();

    return view('pages.local.FormImageFail', compact('agent_name', 'record', 'forms', 'inspector_data', 'company_datas', 'results', 'images', 'categories'));
  }


  public function repair_notice()
  {
    return view('pages.local.RepairNoice');
  }


  public function edit_images($record_id, $id)
  {
    $image = DB::table('check_result_images')
      ->where('check_result_images.record_id', $record_id)
      ->where('check_result_images.item_id', $id)
      ->select(
        'check_result_images.id',
        'check_result_images.image_path',
        'check_result_images.record_id',
      )
      ->get();

    $chk_item = DB::table('check_items')
      ->where('id', $id)
      ->first();

    $car_detail = DB::table('chk_records')
      ->join('vehicles', 'chk_records.veh_id', '=', 'vehicles.veh_id')
      ->where('chk_records.record_id', $record_id)
      ->select(
        'vehicles.plate',
        'vehicles.province',
        'chk_records.updated_at'
      )
      ->first();

    return view('pages.user.imagesEdit', compact('image', 'car_detail', 'chk_item'));
  }

  public function update_image(Request $request)
  {
    $img = DB::table('check_result_images')->where('id', $request->image_id)->first();

    if ($img && file_exists(public_path($img->image_path))) {
      unlink(public_path($img->image_path));
    }
    $item_id = $img->item_id;

    $upload_location = 'upload/';
    $file = $request->file('new_image');
    $extension = $file->getClientOriginalExtension();
    $newName = $item_id . '_' . Carbon::now()->format('Ymd_His') . '.' . $extension;
    $file->move($upload_location, $newName);
    $fileName = $upload_location . $newName;


    DB::table('check_result_images')->where('id', $request->image_id)->update([
      'image_path' => $fileName,
      'updated_at' => Carbon::now(),
    ]);

    return back()->with('success', 'เปลี่ยนภาพเรียบร้อยแล้ว');
  }


  public function delete_image($id)
  {
    $image = DB::table('check_result_images')->where('id', $id)->first();

    if ($image && File::exists(public_path($image->image_path))) {
      unlink(public_path($image->image_path));
    }

    DB::table('check_result_images')->where('id', $id)->delete();

    return back()->with('success', 'ลบรูปภาพเรียบร้อยแล้ว');
  }
}
