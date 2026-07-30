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

class CompanyPreInspectionController extends Controller
{
      public function __construct()
    {
        $this->middleware(['auth', 'role:company']);
    }

    public function index()
    {
        $company_id = Auth::user()->company_id ?? Auth::user()->user_id;

        // ดึงข้อมูลแม่แบบ พร้อมนับจำนวนฟิลด์ย่อย (field_count) ที่อยู่ในแม่แบบนั้น
        $templates = DB::table('pre_inspection_templates')
            ->leftJoin('pre_inspection_fields', 'pre_inspection_templates.id', '=', 'pre_inspection_fields.template_id')
            ->select(
                'pre_inspection_templates.id',
                'pre_inspection_templates.template_name',
                'pre_inspection_templates.is_active',
                DB::raw('COUNT(pre_inspection_fields.id) as field_count')
            )
            ->where('pre_inspection_templates.company_id', $company_id)
            ->groupBy('pre_inspection_templates.id', 'pre_inspection_templates.template_name', 'pre_inspection_templates.is_active')
            ->orderBy('pre_inspection_templates.id', 'desc')
            ->get();

        return view('pages.company.pre_inspection_index', compact('templates'));
    }

    public function show($id)
    {
        $company_id = Auth::user()->company_id ?? Auth::user()->user_id;

        // 1. ดึงข้อมูลแม่แบบหลัก และเช็คสิทธิ์ว่าเป็นของบริษัทนี้หรือไม่
        $template = DB::table('pre_inspection_templates')
            ->where('id', $id)
            ->where('company_id', $company_id)
            ->first();

        // ถ้าไม่มีข้อมูล หรือไม่ใช่ของบริษัทตัวเอง ให้เตะกลับ
        if (!$template) {
            return redirect()->route('company.pre_inspection.index')
                             ->with('error', 'ไม่พบข้อมูลแม่แบบ หรือคุณไม่มีสิทธิ์เข้าถึง');
        }

        // 2. ดึงข้อมูลหัวข้อย่อยทั้งหมด เรียงตามลำดับ (sort_order)
        $fields = DB::table('pre_inspection_fields')
            ->where('template_id', $id)
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        return view('pages.company.pre_inspection_show', compact('template', 'fields'));
    }

    public function create()
    {
        return view('pages.company.pre_inspection_create');
    }

   
    public function pre_ins_store(Request $request)
    {
        // 1. Validation
        $request->validate([
            'template_name'        => 'required|string|max:255',
            'fields'               => 'required|array|min:1',
            'fields.*.field_label' => 'required|string|max:255',
            'fields.*.field_type'  => 'required|in:text,image,gps,document',
            'fields.*.is_required' => 'required|in:0,1',
        ]);

        $company_id = Auth::user()->company_id ?? Auth::user()->user_id; 

        DB::beginTransaction();

        try {
            // 2. Insert ลงตารางหลัก (Templates)
            $templateId = DB::table('pre_inspection_templates')->insertGetId([
                'company_id'    => $company_id,
                'template_name' => $request->template_name,
                'is_active'     => 1,
                'created_at'    => Carbon::now(),
                'updated_at'    => Carbon::now(),
            ]);

            // 3. เตรียมข้อมูล Batch Insert สำหรับตารางย่อย (Fields)
            $insertFields = [];
            $sortOrder = 1;

            foreach ($request->fields as $field) {
                $insertFields[] = [
                    'template_id'  => $templateId,
                    'field_label'  => $field['field_label'],
                    'field_type'   => $field['field_type'],
                    'is_required'  => $field['is_required'],
                    'sort_order'   => $sortOrder,
                    'created_at'   => Carbon::now(),
                    'updated_at'   => Carbon::now(),
                ];
                $sortOrder++;
            }

            // 4. บันทึกข้อมูลฟิลด์ทั้งหมด
            DB::table('pre_inspection_fields')->insert($insertFields);

            DB::commit();

            // เปลี่ยน Route ปลายทางกลับไปยังหน้ารายการที่คุณต้องการ
            return redirect()->route('company.form.index')->with('success', 'สร้างเทมเพลตก่อนตรวจรถเรียบร้อยแล้ว');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

}
