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

class CompanySupplyController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:company']);
    }

    public function create()
    {
        return view('pages.company.supplies_create');
    }

    //บันทึกข้อมูล supply
    public function store(Request $request)
    {

        $user = Auth::user();
        $companyCode = $user->company_code;
        $agencyId = 'TZ-7412';

        $request->validate([
            'supply_name' => 'required|string|max:200',
            'supply_address' => 'required|string',
            'vehicle_limit' => 'nullable|integer|min:0',
            'start_date' => 'nullable|date',
            'expire_date' => 'nullable|date|after_or_equal:start_date',
            'supply_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'supply_user' => 'required|string|unique:users,username',
            'supply_password' => 'required|string|min:6',
        ]);

        do {
            $supId = 'SUP-' . strtoupper(Str::random(6));
            $exists = DB::table('supply_datas')->where('sup_id', $supId)->exists();
        } while ($exists);

        $logoPath = null;
        if ($request->hasFile('supply_logo')) {
            $file = $request->file('supply_logo');
            $filename = $supId . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('logo/supply'), $filename); // เก็บแยกโฟลเดอร์ให้เป็นระเบียบ
            $logoPath = 'logo/supply/' . $filename;
        }

        DB::beginTransaction();

        try {
            DB::table('supply_datas')->insert([
                'company_code' => $companyCode,
                'agency_user_id' => $agencyId,
                'sup_id' => $supId,
                'supply_name' => $request->supply_name,
                'supply_logo' => $logoPath,
                'supply_address' => $request->supply_address,
                'supply_phone' => $request->supply_phone,
                'supply_email' => $request->supply_email,
                'supply_status' => '1',
                'vehicle_limit' => $request->vehicle_limit ?? 0,
                'require_user_approval' => $request->has('require_user_approval') ? 1 : 0,
                'start_date' => $request->start_date,
                'expire_date' => $request->expire_date,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            DB::table('users')->insert([
                'user_id' => $supId,
                'username' => $request->supply_user,
                'prefix' => '-',
                'name' => $request->supply_name,
                'lastname' => '-',
                'user_status' => $request->has('require_user_approval') ? 1 : 0,
                'email' => $request->supply_email,
                'password' => Hash::make($request->supply_password),
                'user_phone' => $request->supply_phone,
                'logo_agency' => $logoPath,
                'role' => 'supply',
                'company_code' => $companyCode,
                'agency_user_id' => $agencyId,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            DB::commit();

            return redirect()
                ->route('company.supplies.index')
                ->with('success', 'สร้างบริษัทในเครือ(Supply) เรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage())->withInput();
        }
    }

    public function SupIndex()
    {
        $companyCode = Auth::user()->company_code;

       $supplies = DB::table('supply_datas')
            ->select('supply_datas.*') 
           ->selectRaw('IFNULL((SELECT COUNT(id) FROM vehicles_detail WHERE vehicles_detail.supply_id = supply_datas.sup_id), 0) as total_vehicles')
            ->where('company_code', $companyCode)
            ->orderBy('supply_name', 'ASC')
            ->get();

        return view('pages.company.supplies_index', compact('supplies'));
    }

    public function edit($id)
    {
        $companyCode = Auth::user()->company_code;

        $supply = DB::table('supply_datas')
            ->where('sup_id', $id)
            ->where('company_code', $companyCode)
            ->first();

        if (!$supply) {
            return redirect()->route('company.supplies.index')->with('error', 'ไม่พบข้อมูลสาขา หรือไม่มีสิทธิ์เข้าถึง');
        }

        $user = DB::table('users')->where('user_id', $id)->first();

        return view('pages.company.supplies_edit', compact('supply', 'user'));
    }

    public function update(Request $request, $id)
    {
        $companyCode = Auth::user()->company_code;

        $supply = DB::table('supply_datas')
            ->where('sup_id', $id)
            ->where('company_code', $companyCode)
            ->first();

        if (!$supply) {
            return redirect()->route('company.supplies.index')->with('error', 'ไม่พบข้อมูลสาขา');
        }

        $request->validate([
            'supply_name' => 'required|string|max:250',
            'supply_address' => 'required|string',
            'vehicle_limit' => 'nullable|integer|min:0',
            'start_date' => 'nullable|date',
            'expire_date' => 'nullable|date|after_or_equal:start_date',
            'supply_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5048',
            'supply_user' => 'required|string',
            'supply_password' => 'nullable|string|min:4',
        ]);

        // จัดการรูปภาพโลโก้
        $logoPath = $supply->supply_logo;
        if ($request->hasFile('supply_logo')) {
            // ลบรูปเก่าทิ้ง
            if ($logoPath && File::exists(public_path($logoPath))) {
                File::delete(public_path($logoPath));
            }
            // อัปโหลดรูปใหม่
            $file = $request->file('supply_logo');
            $filename = $supply->sup_id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('logo/supply'), $filename);
            $logoPath = 'logo/supply/' . $filename;
        }

        // เริ่ม Transaction
        DB::beginTransaction();

        try {
            // อัปเดตตาราง supply_datas
            DB::table('supply_datas')
                ->where('sup_id', $id)
                ->update([
                    'supply_name' => $request->supply_name,
                    'supply_logo' => $logoPath,
                    'supply_address' => $request->supply_address,
                    'supply_phone' => $request->supply_phone,
                    'supply_email' => $request->supply_email,
                    'supply_status' => $request->input('supply_status') == 1 ? '1' : '0',
                    'vehicle_limit' => $request->vehicle_limit ?? 0,
                    'start_date' => $request->start_date,
                    'expire_date' => $request->expire_date,
                    'updated_at' => Carbon::now(),
                ]);

            // เตรียมข้อมูลอัปเดตตาราง users
            $userData = [
                'username' => $request->supply_user,
                'name' => $request->supply_name,
                'email' => $request->supply_email,
                'user_phone' => $request->supply_phone,
                'logo_agency' => $logoPath,
                'user_status' => $request->has('supply_status') ? 1 : 0,
                'updated_at' => Carbon::now(),
            ];

            // ถ้าระบุรหัสผ่านใหม่มา ค่อยอัปเดตรหัสผ่าน
            if ($request->filled('supply_password')) {
                $userData['password'] = Hash::make($request->supply_password);
            }

            // อัปเดตตาราง users
            DB::table('users')->where('user_id', $id)->update($userData);

            DB::commit();

            return redirect()
                ->route('company.supplies.index')
                ->with('success', 'อัปเดตข้อมูล Supply เรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $companyCode = Auth::user()->company_code;

        $supply = DB::table('supply_datas')
            ->where('sup_id', $id)
            ->where('company_code', $companyCode)
            ->first();

        if (!$supply) {
            return redirect()->route('company.index')->with('error', 'ไม่พบข้อมูลสาขา หรือไม่มีสิทธิ์เข้าถึง');
        }

        DB::beginTransaction();

        try {
            $logoPath = $supply->supply_logo;
            if ($logoPath && File::exists(public_path($logoPath))) {
                File::delete(public_path($logoPath));
            }

            DB::table('supply_datas')->where('sup_id', $id)->delete();
            DB::table('users')->where('user_id', $id)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'ลบข้อมูล Supply เรียบร้อยแล้ว'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการลบข้อมูล: ' . $e->getMessage()
            ], 500);
        }
    }

    public function SupShow($id)
    {
        $companyCode = auth()->user()->company_code;

        // 1. ข้อมูล Supply
        $supply = DB::table('supply_datas')
            ->where('sup_id', $id)
            ->where('company_code', $companyCode)
            ->first();

        if (!$supply) abort(404);

        // 2. ข้อมูลรถ 
        $vehicles = DB::table('vehicles_detail')
            ->leftJoin('vehicle_types', 'vehicles_detail.car_type', '=', 'vehicle_types.id')
            ->where('vehicles_detail.supply_id', $id)
            ->select(
                'vehicles_detail.*',
                'vehicle_types.vehicle_type as type_name'
            )
            ->orderBy('vehicles_detail.updated_at', 'DESC')
            ->get();

        // 3. ข้อมูลพนักงาน 
        $drivers = DB::table('drivers_detail')
            ->where('supply_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.company.supplies_show', compact('supply', 'vehicles', 'drivers'));
    }
}
