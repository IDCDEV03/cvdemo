<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Enums\Role;
use Illuminate\Support\Facades\File;

class StaffReportTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:staff']);
    }

    public function index()
    {

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
            ->where('report_templates.is_active', '=', '1')
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

        return view('pages.staff.Template.report_template_index', compact('templates'));
    }

    public function create()
    {
        return view('pages.staff.Template.report_template_create');
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

        DB::beginTransaction();

        try {
            // 2. Insert ลงตารางหลัก (Report Templates)
            $templateId = DB::table('report_templates')->insertGetId([
                'company_id'    => null,
                'template_name' => $request->template_name,
                'header_html'   => $request->header_html,
                'footer_html'   => $request->footer_html,
                'is_active'     => 1,
                'created_at'    => Carbon::now(),
                'updated_at'    => Carbon::now(),
            ]);


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

            return redirect()->route('staff.report_template.index')->with('success', 'สร้างแบบรายงานเรียบร้อยแล้ว!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    public function show($id)
    {

        $template = DB::table('report_templates')
            ->where('id', $id)
            ->first();

        if (!$template) {
            return redirect()->route('company.report_template.index')
                ->with('error', 'ไม่พบข้อมูล หรือไม่มีสิทธิ์เข้าถึง');
        }

        $fields = DB::table('report_template_fields')
            ->where('template_id', $id)
            ->orderBy('sort_order', 'asc')
            ->get();

        return view('pages.staff.Template.report_template_show', compact('template', 'fields'));
    }

    public function report_preview($id)
    {
        $template = DB::table('report_templates')->where('id', $id)->first();

        if (!$template) {
            return redirect()->back()->with('error', 'ไม่พบแม่แบบรายงาน');
        }

        // สร้างข้อมูลจำลอง (Mock Data) เพื่อให้พรีวิวดูสมจริง
        $mockData = [
            '[company_name]'      => 'บริษัท ไอดี ไดรฟ์ จำกัด (มหาชน)',
            '[inspector_name]'    => 'นายสมชาย มั่นคง',
            '[inspect_date]'      => date('d/m/Y'),
            '[car_plate]'         => '70-1234 ขอนแก่น',
            '[car_brand]'         => 'HINO',
            '[car_model]'         => '500 Victor',
            '[car_number_record]' => 'VIN88888888888',
            '[car_type]'          => 'รถบรรทุก 10 ล้อ', // แสดงเป็นข้อความตามที่คุยกันไว้
            '[day_new]'           => date('d/m/Y', strtotime('+15 days')),
            '[time_new]'          => '10:00 น.',
            '[supply_name]'       => 'หน่วยงานขนส่งภาคตะวันออก',
        ];

        // แทนที่ Shortcode ด้วยข้อมูลจำลอง พร้อมใส่ Style ให้รู้ว่าเป็นจุดเติมข้อมูล
        $search = array_keys($mockData);
        // เปลี่ยนรูปแบบการแทนที่ข้อความ
$replace = array_map(function($value) {
    return '<span style="background-color: #e0e7ff; color: #4338ca; padding: 2px 8px; border-radius: 4px; font-weight: 500; font-size: 0.95em; border: 1px solid #c7d2fe;">' . $value . '</span>';
}, array_values($mockData));

        $previewHeader = str_replace($search, $replace, $template->header_html);
        $previewFooter = str_replace($search, $replace, $template->footer_html);

        return view('pages.staff.Template.report_template_preview', compact('template', 'previewHeader', 'previewFooter'));
    }
}
