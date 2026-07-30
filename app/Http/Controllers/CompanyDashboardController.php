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

class CompanyDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:company']);
    }

    public function index()
    {
        // 1. ดึงข้อมูล User ที่ล็อกอินอยู่
        $user = Auth::user();
        $companyCode = $user->company_code;

        // 2. ดึงข้อมูลรายละเอียดของ Company ตัวเอง
        $companyDetails = DB::table('company_details')
            ->where('company_id', $companyCode)
            ->first();

        // 3. ดึงข้อมูลสถิติต่างๆ (จำลองการ Query ไว้ก่อน เผื่อตาราง Supply คุณยังไม่เสร็จ)
        // จำนวน Supply ที่บริษัทนี้สร้างไว้
        $supplyCount = DB::table('users') // สมมติว่าเก็บ Supply ในตาราง users โดยมี role = 'supply'
            ->where('company_code', $companyCode)
            ->where('role', 'supply')
            ->count();

        // จำนวนฟอร์มที่สร้างไปแล้ว (สมมติว่าคุณมีตาราง forms)
        $formCount = DB::table('forms')->where('user_id', '=', $companyCode)->count();
        //$formCount = 0; // ใส่ 0 ไว้ก่อนรอทำระบบฟอร์ม

        // 4. ส่งข้อมูลไปที่หน้า View
        return view('pages.company.dashboard', compact('user', 'companyDetails', 'supplyCount', 'formCount'));
    }

    public function company_form()
    {
        $user = Auth::user()->user_id;

        $form_list = DB::table('forms')
            ->leftJoin('vehicle_types', 'forms.car_type', '=', 'vehicle_types.id')
            ->select('forms.*', 'vehicle_types.vehicle_type')
            ->where('forms.user_id', $user)
            ->orderBy('forms.updated_at', 'DESC')
            ->get();

        return view('pages.company.form_index', compact('form_list'));
    }

    public function vehicles_information(Request $request)
    {
        // เปลี่ยนมาใช้ company_code ตามโครงสร้างตาราง vehicles_detail
        $companyCode = Auth::user()->company_code;
        $filter = $request->get('filter', 'all');

        // รับค่าจากฟอร์มค้นหา
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $searchStatus = $request->get('evaluate_status');

        // 1. ดึงข้อมูลรถทั้งหมดของบริษัทนี้
        $allVehicles = DB::table('vehicles_detail')
            ->join('vehicle_types', 'vehicles_detail.car_type', '=', 'vehicle_types.id')
            ->leftJoin('supply_datas', 'vehicles_detail.supply_id', '=', 'supply_datas.sup_id')
            ->where('vehicles_detail.company_code', $companyCode)
            ->select('vehicles_detail.*', 'vehicle_types.vehicle_type', 'supply_datas.supply_name')
            ->get();

        $passedInspections = 0;
        $waitingInspections = 0;
        $failedInspections = 0;
        $totalInspections = 0;
        $filteredVehicles = [];

        foreach ($allVehicles as $car) {
            // สร้าง Query สำหรับดึงประวัติการตรวจ
            $historyQuery = DB::table('chk_records')
                ->where('veh_id', $car->car_id)
                ->orderBy('created_at', 'desc');

            // กรองตามช่วงวันที่ (ถ้ามีการระบุ)
            if ($startDate && $endDate) {
                $historyQuery->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            }

            // กรองตามสถานะผลประเมินจากการค้นหา (ถ้ามีการระบุ)
            if ($searchStatus) {
                $historyQuery->where('evaluate_status', $searchStatus);
            }

            $history = $historyQuery->get();
            $car->history = $history;
            $car->inspect_count = $history->count();
            $car->latest_record = $history->first();

            //  นับสถิติจาก "สถานะล่าสุด" — ต้องสอดคล้องกับ logic ที่แสดงใน table
            $latest = $car->latest_record;
            if ($car->inspect_count > 0) {
                $totalInspections++;
                if ($latest) {
                    if ($latest->chk_status == '1') {
                        if ($latest->evaluate_status == 1) $passedInspections++;
                        elseif ($latest->evaluate_status == 2) $waitingInspections++;
                        elseif ($latest->evaluate_status == 3) $failedInspections++;
                    } elseif (in_array($latest->chk_status, ['0', '2']) && is_null($latest->evaluate_status)) {
                        $failedInspections++;
                    }
                }
            }

            //  กรองรถเข้าตาราง
            $shouldInclude = false;

            // กรณีไม่มีการค้นหา (โชว์ตามปุ่ม filter ปกติ)
            if (!$startDate && !$endDate && !$searchStatus) {
                if ($filter == 'all') {
                    $shouldInclude = $car->inspect_count > 0;
                } elseif ($latest) {
                    if ($latest->chk_status == '1') {
                        if ($filter == 'passed' && $latest->evaluate_status == 1) $shouldInclude = true;
                        elseif ($filter == 'waiting' && $latest->evaluate_status == 2) $shouldInclude = true;
                        elseif ($filter == 'failed' && $latest->evaluate_status == 3) $shouldInclude = true;
                    } elseif (in_array($latest->chk_status, ['0', '2']) && is_null($latest->evaluate_status)) {
                        if ($filter == 'failed') $shouldInclude = true;
                    }
                }
            } else {
                // กรณีมีการค้นหาด้วย date range — ใช้ filter ร่วมด้วย
                if ($car->inspect_count > 0) {
                    if ($filter == 'all' || !$filter) {
                        $shouldInclude = true;
                    } elseif ($latest) {
                        if ($latest->chk_status == '1') {
                            if ($filter == 'passed' && $latest->evaluate_status == 1) $shouldInclude = true;
                            elseif ($filter == 'waiting' && $latest->evaluate_status == 2) $shouldInclude = true;
                            elseif ($filter == 'failed' && $latest->evaluate_status == 3) $shouldInclude = true;
                        } elseif (in_array($latest->chk_status, ['0', '2']) && is_null($latest->evaluate_status)) {
                            if ($filter == 'failed') $shouldInclude = true;
                        }
                    }
                }
            }

            if ($shouldInclude) {
                $filteredVehicles[] = $car;
            }
        }

        // ==========================================
        // 💡 เพิ่มส่วนการเรียงลำดับ (Sorting)
        // ==========================================
        usort($filteredVehicles, function ($a, $b) {
            // ดึงวันที่ตรวจล่าสุดออกมา ถ้าไม่มีให้เป็น null
            $dateA = $a->latest_record ? $a->latest_record->created_at : null;
            $dateB = $b->latest_record ? $b->latest_record->created_at : null;

            // กรณีที่ไม่มีข้อมูลการตรวจทั้งคู่ ให้คงลำดับเดิม
            if ($dateA == $dateB) return 0;

            // ถ้ารถคัน A ไม่มีข้อมูลการตรวจ (null) ให้เอาคัน A ไปไว้ข้างหลัง (return 1)
            if (is_null($dateA)) return 1;

            // ถ้ารถคัน B ไม่มีข้อมูลการตรวจ (null) ให้เอาคัน A ไว้ข้างหน้า (return -1)
            if (is_null($dateB)) return -1;

            // เปรียบเทียบวันที่ (เรียงจากใหม่ไปเก่า - Descending Order)
            return $dateA < $dateB ? 1 : -1;
        });


        $vehicles = $filteredVehicles;

        return view('pages.company.vehicles_information', compact(
            'vehicles',
            'passedInspections',
            'waitingInspections',
            'failedInspections',
            'totalInspections',
            'filter'
        ));
    }

    public function failedItems($record_id)
    {
        $companyCode = Auth::user()->company_code;

        // ตรวจสอบว่า record นี้เป็นของบริษัทนี้
        $record = DB::table('chk_records as cr')
            ->join('vehicles_detail as v', 'v.car_id', '=', 'cr.veh_id')
            ->where('cr.record_id', $record_id)
            ->where('v.company_code', $companyCode)
            ->select('cr.record_id')
            ->first();

        if (!$record) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $items = DB::table('check_records_result as crr')
            ->join('check_items as ci', 'ci.item_id', '=', 'crr.item_id')
            ->leftJoin('check_categories as cc', 'cc.category_id', '=', 'ci.category_id')
            ->where('crr.record_id', $record_id)
            ->where('crr.result_status', '0')
            ->select(
                'cc.chk_cats_name as category_name',
                'ci.item_name',
                'crr.result_value',
                'crr.user_comment'
            )
            ->orderBy('cc.cates_no')
            ->orderBy('ci.item_no')
            ->get();

        return response()->json($items);
    }
}
