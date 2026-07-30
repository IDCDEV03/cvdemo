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

class CompanyVehicleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:company']);
    }

      public function VehiclesList()
    {
        $veh_list = DB::table('vehicles_detail')
            ->select('vehicles_detail.car_id', 'vehicles_detail.car_plate', 'vehicles_detail.car_brand', 'vehicles_detail.car_model', 'users.company_code', 'users.name', 'vehicle_types.vehicle_type', 'vehicles_detail.status', 'vehicles_detail.created_at','supply_datas.supply_name')
            ->leftjoin('users', 'users.company_code', '=', 'vehicles_detail.company_code')
            ->leftjoin('supply_datas', 'vehicles_detail.supply_id', 'supply_datas.sup_id')
            ->leftjoin('vehicle_types', 'vehicles_detail.car_type', 'vehicle_types.id')
            ->orderBy('vehicles_detail.created_at', 'DESC')
            ->groupBy('vehicles_detail.car_id')
            ->get();

        return view('pages.company.vehicles_list', compact('veh_list'));
    }

    public function create($supply_id)
    {
        $companyCode = Auth::user()->company_code;

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

        $supply = DB::table('supply_datas')
            ->where('sup_id', $supply_id)
            ->where('company_code', $companyCode)
            ->first();

        if (!$supply) {
            return redirect()->route('company.supplies.index')->with('error', 'ไม่พบข้อมูลสาขา');
        }

        return view('pages.company.vehicles_create', compact('supply', 'car_type', 'province', 'car_brand'));
    }

    public function store(Request $request, $supply_id)
    {
        $user = Auth::user();
        $veh_id = 'VEH-' . Str::upper(Str::random(9));

        // 1. Validate ข้อมูลที่ส่งมาจากฟอร์ม
        $request->validate([
            'plate'             => 'required|string|max:10',
            'province'          => 'required|not_in:0',
            'veh_brand'         => 'required|not_in:0',
            'vehicle_type'      => 'required|not_in:0',
            'car_model'         => 'nullable|string|max:50',
            'car_number_record' => 'nullable|string|max:50',
            'car_age'           => 'nullable|string|max:20',
            'car_mileage'       => 'nullable|string|max:20',
            'car_tax'           => 'nullable|string|max:100',
            'car_insure'        => 'nullable|string|max:100',
            'vehicle_image'     => 'nullable|image|mimes:jpeg,png,jpg|max:5048',
        ], [
            'province.not_in'     => 'กรุณาเลือกจังหวัด',
            'veh_brand.not_in'    => 'กรุณาเลือกยี่ห้อรถ',
            'vehicle_type.not_in' => 'กรุณาเลือกประเภทรถ',
        ]);

        $plate = $request->plate;

        try {
            // 3. จัดการอัปโหลดภาพถ่ายรถ (ถ้ามีการอัปโหลด)
            $imagePath = null;
            if ($request->hasFile('vehicle_image')) {
                $file = $request->file('vehicle_image');
                $filename = $plate . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('images/vehicles'), $filename);
                $imagePath = 'images/vehicles/' . $filename;
            }

            // 4.ทะเบียนรถ + จังหวัด
            $fullCarPlate = trim($request->plate . ' ' . $request->province);

            // 5. บันทึก
            $vehicleData = [
                'user_id'           => $user->user_id,
                'company_code'      => $user->company_code,
                'supply_id'         => $supply_id,
                'car_id'            => $veh_id,
                'car_plate'         => $fullCarPlate,
                'car_brand'         => $request->veh_brand,
                'car_model'         => $request->car_model,
                'car_number_record' => $request->car_number_record,
                'car_age'           => $request->car_age,
                'car_tax'           => $request->car_tax,
                'car_mileage'       => $request->car_mileage,
                'car_insure'        => $request->car_insure,
                'car_type'          => $request->vehicle_type,
                'car_image'         => $imagePath,
                'car_trailer_plate'    => $request->car_trailer_plate,
                'car_register_date'    => $request->car_register_date,
                'car_insurance_expire' => $request->car_insurance_expire,
                'car_total_weight'     => $request->car_total_weight,
                'car_fuel_type'        => $request->car_fuel_type,
                'status'            => $request->input('status') == 1 ? '1' : '0',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ];

            $vehicleDbId = DB::table('vehicles_detail')->insertGetId($vehicleData);

            DB::table('vehicle_activity_logs')->insert([
                'vehicle_id'  => $vehicleDbId,
                'user_id'     => $user->user_id,
                'action'      => 'create',
                'before_data' => null,
                'after_data'  => json_encode($vehicleData, JSON_UNESCAPED_UNICODE),
                'created_at'  => Carbon::now(),
            ]);

            return redirect()->route('company.supplies.show', $supply_id)
                ->with('success', "ลงทะเบียน {$fullCarPlate} เรียบร้อยแล้ว");
        } catch (\Exception $e) {
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $companyCode = Auth::user()->company_code;

        // 1. ดึงข้อมูลรถที่ต้องการแก้ไข
        $car = DB::table('vehicles_detail')
            ->where('id', $id)
            ->where('company_code', $companyCode) // ป้องกันการแอบแก้รถข้ามบริษัท
            ->first();

        if (!$car) abort(404);

        // 2. แยกทะเบียนรถกับจังหวัดออกจากกัน (เพราะเราเก็บรวมกันไว้ใน car_plate)
        $plateParts = explode(' ', $car->car_plate);
        $provinceName = array_pop($plateParts); // ดึงคำสุดท้ายออกมา (จังหวัด)
        $plateNumber = implode(' ', $plateParts); // ส่วนที่เหลือคือเลขทะเบียน

        // 3. ดึงข้อมูลสำหรับ Dropdown (จังหวัด, ยี่ห้อ, ประเภท)
        $province = DB::table('provinces')
            ->orderBy('name_th', 'ASC')
            ->get();
        $car_brand = DB::table('car_brands')->orderBy('brand_name', 'ASC')->get();
        $car_type = DB::table('vehicle_types')->orderBy('id', 'ASC')->get();

        // 4. ดึงข้อมูล Supply เพื่อใช้ในการทำ Breadcrumb หรือ Link ย้อนกลับ
        $supply = DB::table('supply_datas')->where('sup_id', $car->supply_id)->first();

        return view('pages.company.vehicles_edit', compact('car', 'plateNumber', 'provinceName', 'province', 'car_brand', 'car_type', 'supply'));
    }

    public function car_update(Request $request, $id)
    {
        $request->validate([
            'plate'         => 'required|string|max:10',
            'province'      => 'required|not_in:0',
            'veh_brand'     => 'required|not_in:0',
            'vehicle_type'  => 'required|not_in:0',
            'vehicle_image' => 'nullable|image|mimes:jpeg,png,jpg|max:5048',
        ]);

        $user = Auth::user();
        $plate = $request->plate;

        // 1. ดึงข้อมูล "ก่อนแก้" (Original Data) เก็บไว้ก่อน
        $oldData = DB::table('vehicles_detail')->where('id', $id)->first();
        if (!$oldData) return back()->with('error', 'ไม่พบข้อมูลรถ');

        try {
            // จัดการรูปภาพ (โค้ดเดิมของคุณ)
            $imagePath = $oldData->car_image;
            if ($request->hasFile('vehicle_image')) {
                if ($oldData->car_image && file_exists(public_path($oldData->car_image))) {
                    unlink(public_path($oldData->car_image));
                }
                $file = $request->file('vehicle_image');
                $filename = $plate . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('images/vehicles'), $filename);
                $imagePath = 'images/vehicles/' . $filename;
            }

            $fullCarPlate = trim($request->plate . ' ' . $request->province);

            // 2. เตรียมข้อมูลใหม่ "หลังแก้"
            $newData = [
                'car_plate'         => $fullCarPlate,
                'car_brand'         => $request->veh_brand,
                'car_model'         => $request->car_model,
                'car_number_record' => $request->car_number_record,
                'car_age'           => $request->car_age,
                'car_tax'           => $request->car_tax,
                'car_mileage'       => $request->car_mileage,
                'car_insure'        => $request->car_insure,
                'car_type'          => $request->vehicle_type,
                'car_image'         => $imagePath,
                'car_trailer_plate'    => $request->car_trailer_plate,
                'car_register_date'    => $request->car_register_date,
                'car_insurance_expire' => $request->car_insurance_expire,
                'car_total_weight'     => $request->car_total_weight,
                'car_fuel_type'        => $request->car_fuel_type,
                'status'            => $request->status,
                'updated_at'        => Carbon::now(),
            ];

            // 3. เริ่มต้นกระบวนการ Database Transaction เพื่อความปลอดภัย
            DB::transaction(function () use ($id, $user, $oldData, $newData) {

                // อัปเดตข้อมูลรถ
                DB::table('vehicles_detail')->where('id', $id)->update($newData);

                // 4. บันทึก Log การแก้ไข
                // เราจะเก็บเฉพาะฟิลด์ที่มีการเปลี่ยนแปลงจริงๆ เพื่อประหยัดพื้นที่
                $changedBefore = [];
                $changedAfter = [];

                foreach ($newData as $key => $value) {
                    // ข้ามการเช็ค updated_at
                    if ($key == 'updated_at') continue;

                    if (isset($oldData->$key) && $oldData->$key != $value) {
                        $changedBefore[$key] = $oldData->$key;
                        $changedAfter[$key] = $value;
                    }
                }

                // ถ้ามีการเปลี่ยนแปลงจริง จึงจะบันทึก Log
                if (!empty($changedAfter)) {
                    DB::table('vehicle_activity_logs')->insert([
                        'vehicle_id'  => $id,
                        'user_id'     => $user->user_id,
                        'action'      => 'update',
                        'before_data' => json_encode($changedBefore, JSON_UNESCAPED_UNICODE),
                        'after_data'  => json_encode($changedAfter, JSON_UNESCAPED_UNICODE),
                        'created_at'  => Carbon::now(),
                    ]);
                }
            });

            return redirect()->route('company.supplies.show', $oldData->supply_id)
                ->with('success', "แก้ไขข้อมูล {$fullCarPlate} เรียบร้อยแล้ว");
        } catch (\Exception $e) {
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage())->withInput();
        }
    }

    public function car_destroy($id)
    {
        $user = Auth::user();

        // 1. ค้นหาข้อมูลรถที่ต้องการลบ
        $car = DB::table('vehicles_detail')->where('id', $id)->first();
        if (!$car) {
            return response()->json([
                'success' => false,
                'message' => 'ไม่พบข้อมูลรถในระบบ'
            ], 404);
        }

        try {
            DB::transaction(function () use ($id, $user, $car) {

                // 2. บันทึก Log ว่าใครเป็นคนลบ และลบข้อมูลอะไรไปบ้าง 
                DB::table('vehicle_activity_logs')->insert([
                    'vehicle_id'  => $id,
                    'user_id'     => $user->user_id ?? $user->id,
                    'action'      => 'delete',
                    'before_data' => json_encode($car, JSON_UNESCAPED_UNICODE),
                    'after_data'  => null,
                    'created_at'  => now(),
                ]);

                // 3. ลบไฟล์รูปภาพของรถคันนี้ออกจาก Server (ถ้ามี)
                if ($car->car_image && file_exists(public_path($car->car_image))) {
                    unlink(public_path($car->car_image));
                }

                // 4. ลบข้อมูลรถออกจาก Database (โควตาจะถูกคืนกลับมาอัตโนมัติ)
                DB::table('vehicles_detail')->where('id', $id)->delete();
            });

            return response()->json([
                'success' => true,
                'message' => "ลบรถทะเบียน {$car->car_plate} เรียบร้อยแล้ว"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ], 500);
        }
    }
}
