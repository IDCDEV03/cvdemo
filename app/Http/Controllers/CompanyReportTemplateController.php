<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Enums\Role;
use Illuminate\Support\Facades\File;

class CompanyReportTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:company']);
    }

    public function index()
    {
        $company_id = Auth::user()->company_id ?? Auth::user()->user_id;

        // ดึงข้อมูลแม่แบบรายงาน พร้อมนับจำนวนฟิลด์ย่อย (Custom Fields)
        $templates = DB::table('report_templates')
            ->leftJoin('report_template_fields', 'report_templates.id', '=', 'report_template_fields.template_id')
            ->select(
                'report_templates.id',
                'report_templates.template_name',
                'report_templates.header_html',
                'report_templates.footer_html',
                'report_templates.is_active',
                'report_templates.created_at',
                DB::raw('COUNT(report_template_fields.id) as field_count')
            )
            ->where('report_templates.company_id', $company_id)
            // เมื่อใช้ COUNT ต้อง Group By คอลัมน์ที่ Select มาทั้งหมด
            ->groupBy(
                'report_templates.id', 
                'report_templates.template_name', 
                'report_templates.header_html', 
                'report_templates.footer_html', 
                'report_templates.is_active',
                'report_templates.created_at'
            )
            ->orderBy('report_templates.id', 'desc')
            ->get();

        return view('pages.company.report_template_index', compact('templates'));
    }

    public function create()
    {
        return view('pages.company.report_template_create');
    }

    public function store(Request $request)
    {
        // 1. Validation
        $request->validate([
            'template_name' => 'required|string|max:255',
            'header_html'   => 'nullable|string',
            'footer_html'   => 'nullable|string',
            'fields'               => 'nullable|array', // อนุญาตให้ไม่มีฟิลด์ Custom ก็ได้
            'fields.*.field_label' => 'required_with:fields|string|max:255',
            'fields.*.field_key'   => 'required_with:fields|string|alpha_dash|max:50', // บังคับให้เป็นภาษาอังกฤษ/ตัวเลข/ขีดล่าง เพื่อเป็นรหัสตัวแปร
            'fields.*.field_type'  => 'required_with:fields|in:text,number,date',
        ]);

        $company_id = Auth::user()->company_id ?? Auth::user()->user_id;

        DB::beginTransaction();

        try {
            // 2. Insert ลงตารางหลัก (Report Templates)
            $templateId = DB::table('report_templates')->insertGetId([
                'company_id'    => $company_id,
                'template_name' => $request->template_name,
                'header_html'   => $request->header_html,
                'footer_html'   => $request->footer_html,
                'is_active'     => 1,
                'created_at'    => Carbon::now(),
                'updated_at'    => Carbon::now(),
            ]);

            // 3. เตรียมข้อมูลและ Insert ตารางย่อย (Custom Fields)
            if ($request->has('fields')) {
                $insertFields = [];
                $sortOrder = 1;

                foreach ($request->fields as $field) {
                    $insertFields[] = [
                        'template_id'  => $templateId,
                        'field_label'  => $field['field_label'],
                        'field_key'    => strtolower($field['field_key']), // ทำให้เป็นตัวเล็กเสมอ
                        'field_type'   => $field['field_type'],
                        'is_required'  => $field['is_required'] ?? 0,
                        'sort_order'   => $sortOrder,
                        'created_at'   => Carbon::now(),
                        'updated_at'   => Carbon::now(),
                    ];
                    $sortOrder++;
                }

                DB::table('report_template_fields')->insert($insertFields);
            }

            DB::commit();

            return redirect()->route('company.report_template.show')->with('success', 'สร้างแบบรายงานเรียบร้อยแล้ว!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $company_id = Auth::user()->company_id ?? Auth::user()->user_id;
        
        // 1. ดึงข้อมูลแม่แบบหลัก
        $template = DB::table('report_templates')
            ->where('id', $id)
            ->where('company_id', $company_id)
            ->first();

        if (!$template) {
            return redirect()->route('company.report_template.index')
                             ->with('error', 'ไม่พบข้อมูล หรือไม่มีสิทธิ์เข้าถึง');
        }

        // 2. ดึงข้อมูลตัวแปรเฉพาะกิจ (Custom Fields)
        $fields = DB::table('report_template_fields')
            ->where('template_id', $id)
            ->orderBy('sort_order', 'asc')
            ->get();

        return view('pages.company.report_template_show', compact('template', 'fields'));
    }

    public function indexFormGroup()
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
            ->orderBy('form_groups.id', 'desc')
            ->get();

        return view('pages.company.form_group_index', compact('formGroups'));
    }

    public function createFormGroup()
    {
        // ดึงข้อมูลแม่แบบทั้ง 3 ส่วนมาทำ Dropdown
        $preInspections = DB::table('pre_inspection_templates')->get();        
        $checkItems = DB::table('forms')->get(); 
        $reports = DB::table('report_templates')->get();        
        $companies = DB::table('company_details')->get();

        return view('pages.company.form_group_create', compact('preInspections', 'checkItems', 'reports', 'companies'));
    }

    public function storeFormGroup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            // ตรวจสอบอย่างน้อยต้องมีฟอร์มตรวจหลัก
            'check_item_group_id' => 'required|integer', 
        ]);

        $isSystemDefault = $request->has('is_system_default') ? 1 : 0;
        // ถ้าเป็น System Default ให้ company_id เป็น null อัตโนมัติ
        $companyId = $isSystemDefault ? null : $request->company_id;

        DB::table('form_groups')->insert([
            'name' => $request->name,
            'description' => $request->description,
            'is_system_default' => $isSystemDefault,
            'company_id' => $companyId,
            'pre_inspection_template_id' => $request->pre_inspection_template_id,
            'check_item_group_id' => $request->check_item_group_id,
            'report_template_id' => $request->report_template_id,
            'created_by' => auth()->id(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return redirect()->route('company.form-groups.index')->with('success', 'สร้างกลุ่มฟอร์มสำเร็จเรียบร้อย');
    }

    public function showFormGroup($id)
    {
        $formGroup = DB::table('form_groups')
            ->leftJoin('companies', 'form_groups.company_id', '=', 'companies.id')
            ->leftJoin('users', 'form_groups.created_by', '=', 'users.id')
            ->leftJoin('pre_inspection_templates', 'form_groups.pre_inspection_template_id', '=', 'pre_inspection_templates.id')
            ->leftJoin('check_item_groups', 'form_groups.check_item_group_id', '=', 'check_item_groups.id')
            ->leftJoin('report_templates', 'form_groups.report_template_id', '=', 'report_templates.id')
            ->select(
                'form_groups.*',
                'companies.name as company_name',
                'users.name as creator_name',
                'pre_inspection_templates.name as pre_name',
                'check_item_groups.name as check_name',
                'report_templates.name as report_name'
            )
            ->where('form_groups.id', $id)
            ->whereNull('form_groups.deleted_at')
            ->first();

        if (!$formGroup) {
            return redirect()->route('company.form-groups.index')->with('error', 'ไม่พบข้อมูลกลุ่มฟอร์ม');
        }

        return view('pages.company.form_group_show', compact('formGroup'));
    }
}
