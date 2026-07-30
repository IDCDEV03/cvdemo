<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Enums\Role;


class FormGroupController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:staff']);
    }

    public function index()
    {
        $formGroups = DB::table('form_groups')
            ->leftJoin('company_details', 'form_groups.company_id', '=', 'company_details.company_id')
            ->leftJoin('users', 'form_groups.created_by', '=', 'users.user_id')
            ->select(
                'form_groups.*',
                'company_details.company_name',
                'users.name as creator_name'
            )
            ->whereNull('form_groups.deleted_at')
            ->groupBy(
                'form_groups.id',
            )
            ->orderBy('form_groups.id', 'desc')
            ->get();

        return view('pages.staff.Form_Group.form_group_index', compact('formGroups'));
    }

    public function createFormGroup()
    {
        // ดึงข้อมูลแม่แบบทั้ง 3 ส่วนมาทำ Dropdown
        $preInspections = DB::table('pre_inspection_templates')->get();
        $checkItems = DB::table('forms')->get();
        $reports = DB::table('report_templates')->get();
        $companies = DB::table('company_details')->get();

        return view('pages.staff.Form_Group.form_group_create', compact('preInspections', 'checkItems', 'reports', 'companies'));
    }

    public function store(Request $request)
    {
        $user = Auth::user()->user_id;
        $form_group_id = 'FG_' . Str::upper(Str::random(8));

        $request->validate([
            // บังคับเลือกบริษัท ถ้าไม่ได้ติ๊ก System Default
            'company_id'          => 'required_unless:is_system_default,1',
            'check_item_form_id' => 'required|exists:forms,form_id',
            'pre_inspection_template_id' => 'nullable|integer',
            'report_template_id'         => 'nullable|integer',
        ], [
            // สามารถ Custom ข้อความแจ้งเตือนภาษาไทยได้ตรงนี้
            'company_id.required_unless' => 'กรุณาระบุบริษัทที่ใช้งาน หรือเลือกเป็นฟอร์มส่วนกลาง',
        ]);

        // 2. จัดการตัวแปร
        $isSystemDefault = $request->has('is_system_default') ? 1 : 0;
        $companyId = $isSystemDefault ? null : $request->company_id;

        // 3. บันทึกข้อมูล
        DB::table('form_groups')->insert([
            'form_group_id'                    => $form_group_id,
            'form_group_name'            => $request->name,
            'description'                => $request->description,
            'is_system_default'          => $isSystemDefault,
            'company_id'                 => $companyId,
            'pre_inspection_template_id' => $request->pre_inspection_template_id,
            'check_item_form_id'        => $request->check_item_form_id,
            'report_template_id'         => $request->report_template_id,
            'is_active'                  => 1,
            'created_by'                 => $user,
            'created_at'                 => Carbon::now(),
            'updated_at'                 => Carbon::now(),
        ]);

        return redirect()->route('staff.form-group.show', ['id' => $form_group_id])
            ->with('success', 'สร้างกลุ่มฟอร์มสำเร็จเรียบร้อย');
    }

    public function show($id)
    {
        // ดึงข้อมูลฟอร์มหลัก พร้อม Join ตารางเพื่อเอา "ชื่อ" ของแม่แบบต่างๆ และบริษัทมาแสดง
        $formGroup = DB::table('form_groups')
            ->leftJoin('company_details', 'form_groups.company_id', '=', 'company_details.company_id')
            ->leftJoin('pre_inspection_templates', 'form_groups.pre_inspection_template_id', '=', 'pre_inspection_templates.id')
            ->leftJoin('forms', 'form_groups.check_item_form_id', '=', 'forms.form_id')
            ->leftJoin('report_templates', 'form_groups.report_template_id', '=', 'report_templates.id')
            ->select(
                'form_groups.*',
                'company_details.company_name',
                'pre_inspection_templates.template_name as pre_name',
                'forms.form_name as check_name', // ปรับชื่อคอลัมน์ form_name ตามที่คุณมี
                'report_templates.template_name as report_name'
            )
            ->where('form_groups.form_group_id', $id)
            ->first();

        if (!$formGroup) {
            return redirect()->route('staff.form-group.index')->with('error', 'ไม่พบข้อมูลกลุ่มฟอร์มนี้');
        }

        return view('pages.staff.Form_Group.form_group_show', compact('formGroup'));
    }

    public function edit($id)
    {
        // ดึงข้อมูลฟอร์มปัจจุบัน
        $formGroup = DB::table('form_groups')->where('form_group_id', $id)->first();

        if (!$formGroup) {
            return redirect()->route('staff.form-group.index')->with('error', 'ไม่พบข้อมูลกลุ่มฟอร์ม');
        }

        // ดึงข้อมูลสำหรับทำ Dropdown (เหมือนหน้า Create)
        $companies = DB::table('company_details')->get();
        $preInspections = DB::table('pre_inspection_templates')->get();
        $checkItems = DB::table('forms')->get();
        $reports = DB::table('report_templates')->get();

        return view('pages.staff.Form_Group.form_group_edit', compact('formGroup', 'companies', 'preInspections', 'checkItems', 'reports'));
    }

    public function update(Request $request, $id)
{
    $user = Auth::user()->user_id;
    // 1. ตรวจสอบข้อมูล (จุดสำคัญ: unique ต้องละเว้น ID ของตัวเอง)
    $request->validate([     
        'name'                => 'required|string|max:255',
        'is_system_default'   => 'nullable|boolean',
        'check_item_form_id' => 'required|exists:forms,form_id',
        'pre_inspection_template_id' => 'nullable|integer',
        'report_template_id'         => 'nullable|integer',
        'is_active'                  => 'required|boolean' // เพิ่มการอัปเดตสถานะ
    ], [
        'company_id.required_unless' => 'กรุณาระบุบริษัทที่ใช้งาน หรือเลือกเป็นฟอร์มส่วนกลาง',
        'form_group_id.unique'             => 'รหัสฟอร์มนี้มีการใช้งานแล้ว'
    ]);

    $isSystemDefault = $request->has('is_system_default') ? 1 : 0;
    $companyId = $isSystemDefault ? null : $request->company_id;

    // 2. อัปเดตข้อมูล
    DB::table('form_groups')->where('form_group_id', $id)->update([
        'form_group_id'              => $request->form_group_id,
        'form_group_name'            => $request->name,
        'description'                => $request->description,
        'is_system_default'          => $isSystemDefault,
        'company_id'                 => $companyId,
        'pre_inspection_template_id' => $request->pre_inspection_template_id,
        'check_item_form_id'        => $request->check_item_form_id,
        'report_template_id'         => $request->report_template_id,
        'is_active'                  => '1',
        'updated_by'                 =>  $user, // เก็บประวัติคนแก้ไข
        'updated_at'                 => Carbon::now(),
    ]);

    return redirect()->route('staff.form-group.show', $id)
                     ->with('success', 'อัปเดตข้อมูลกลุ่มฟอร์มเรียบร้อยแล้ว');
}
}
