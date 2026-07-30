<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class StaffSupplyController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:staff']);
    }

    public function index()
    {
        $supplies = DB::table('supply_datas as s')
            ->join('company_details as c', 'c.company_id', '=', 's.company_code')
            ->leftJoin('users as u', 'u.user_id', '=', 's.sup_id')
            ->select(
                's.sup_id', 's.supply_name', 's.supply_phone', 's.supply_email',
                's.supply_status', 's.created_at',
                'c.company_name',
                'u.username'
            )
            ->orderBy('c.company_name')
            ->orderBy('s.supply_name')
            ->get();

        return view('pages.staff.supply.index', compact('supplies'));
    }

    public function create()
    {
        $companies = DB::table('company_details')
            ->select('company_id', 'company_name')
            ->orderBy('company_name')
            ->get();

        return view('pages.staff.supply.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_code'  => 'required',
            'supply_name'   => 'required|string|max:200',
            'supply_address'=> 'nullable|string',
            'supply_phone'  => 'nullable|string|max:50',
            'supply_email'  => 'nullable|email|max:50',
            'supply_status' => 'required|in:0,1',
            'username'      => 'required|string|max:30|unique:users,username',
            'password'      => 'required|string|min:4',
            'supply_logo'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'company_code.required'  => 'กรุณาเลือกบริษัท',
            'supply_name.required'   => 'กรุณากรอกชื่อ Supply',
            'username.required'      => 'กรุณากรอก Username',
            'username.unique'        => 'Username นี้ถูกใช้งานแล้ว',
            'password.required'      => 'กรุณากรอกรหัสผ่าน',
            'password.min'           => 'รหัสผ่านต้องมีอย่างน้อย 4 ตัวอักษร',
            'password.confirmed'     => 'รหัสผ่านไม่ตรงกัน',
        ]);

        $supId   = 'SUP-' . Str::upper(Str::random(10));
        $logoPath = null;

        if ($request->hasFile('supply_logo')) {
            $file     = $request->file('supply_logo');
            $filename = Carbon::now()->format('Ymd_His') . '_' . $supId . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('logo'), $filename);
            $logoPath = 'logo/' . $filename;
        }

        DB::beginTransaction();
        try {
            DB::table('supply_datas')->insert([
                'company_code'   => $request->company_code,
                'sup_id'         => $supId,
                'supply_name'    => $request->supply_name,
                'supply_logo'    => $logoPath,
                'supply_address' => $request->supply_address,
                'supply_phone'   => $request->supply_phone,
                'supply_email'   => $request->supply_email,
                'supply_status'  => $request->supply_status,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now(),
            ]);

            DB::table('users')->insert([
                'user_id'      => $supId,
                'username'     => $request->username,
                'prefix'       => '-',
                'name'         => $request->supply_name,
                'lastname'     => '-',
                'user_status'  => $request->supply_status,
                'email'        => $request->supply_email,
                'password'     => Hash::make($request->password),
                'user_phone'   => $request->supply_phone,
                'role'         => 'supply',
                'company_code' => $request->company_code,
                'logo_agency'  => $logoPath,
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now(),
            ]);

            DB::commit();
            return redirect()->route('staff.supplies.index')->with('success', "เพิ่ม Supply \"{$request->supply_name}\" สำเร็จ");
        } catch (\Exception $e) {
            DB::rollBack();
            if ($logoPath && file_exists(public_path($logoPath))) {
                unlink(public_path($logoPath));
            }
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $supply = DB::table('supply_datas as s')
            ->join('company_details as c', 'c.company_id', '=', 's.company_code')
            ->leftJoin('users as u', 'u.user_id', '=', 's.sup_id')
            ->where('s.sup_id', $id)
            ->select('s.*', 'c.company_name', 'u.username', 'u.user_status')
            ->first();

        if (!$supply) abort(404);

        $vehicles = DB::table('vehicles_detail as v')
            ->leftJoin('vehicle_types as vt', 'vt.id', '=', 'v.car_type')
            ->where('v.supply_id', $id)
            ->select('v.car_id', 'v.car_plate', 'v.car_brand', 'v.car_model', 'v.car_number_record', 'v.status', 'vt.vehicle_type as type_name')
            ->orderBy('v.id', 'ASC')
            ->get();

        $drivers = DB::table('drivers_detail')
            ->where('supply_id', $id)
            ->whereNull('deleted_at')
            ->orderBy('name', 'ASC')
            ->get();

        return view('pages.staff.supply.show', compact('supply', 'vehicles', 'drivers'));
    }

    public function edit($id)
    {
        $supply = DB::table('supply_datas as s')
            ->join('users as u', 'u.user_id', '=', 's.sup_id')
            ->where('s.sup_id', $id)
            ->select('s.*', 'u.username', 'u.user_status')
            ->first();

        if (!$supply) abort(404);

        $companies = DB::table('company_details')
            ->select('company_id', 'company_name')
            ->orderBy('company_name')
            ->get();

        return view('pages.staff.supply.edit', compact('supply', 'companies'));
    }

    public function update(Request $request, $id)
    {
        $supply = DB::table('supply_datas')->where('sup_id', $id)->first();
        if (!$supply) abort(404);

        $request->validate([
            'company_code'  => 'required',
            'supply_name'   => 'required|string|max:200',
            'supply_address'=> 'nullable|string',
            'supply_phone'  => 'nullable|string|max:50',
            'supply_email'  => 'nullable|email|max:50',
            'supply_status' => 'required|in:0,1',
            'username'      => 'required|string|max:30|unique:users,username,' . $id . ',user_id',
            'password'      => 'nullable|string|min:4|confirmed',
            'supply_logo'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'company_code.required' => 'กรุณาเลือกบริษัท',
            'supply_name.required'  => 'กรุณากรอกชื่อ Supply',
            'username.required'     => 'กรุณากรอก Username',
            'username.unique'       => 'Username นี้ถูกใช้งานแล้ว',
            'password.min'          => 'รหัสผ่านต้องมีอย่างน้อย 4 ตัวอักษร',
            'password.confirmed'    => 'รหัสผ่านไม่ตรงกัน',
        ]);

        $logoPath = $supply->supply_logo;

        if ($request->hasFile('supply_logo')) {
            if ($logoPath && file_exists(public_path($logoPath))) {
                unlink(public_path($logoPath));
            }
            $file     = $request->file('supply_logo');
            $filename = Carbon::now()->format('Ymd_His') . '_' . $id . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('logo'), $filename);
            $logoPath = 'logo/' . $filename;
        }

        DB::beginTransaction();
        try {
            DB::table('supply_datas')->where('sup_id', $id)->update([
                'company_code'   => $request->company_code,
                'supply_name'    => $request->supply_name,
                'supply_logo'    => $logoPath,
                'supply_address' => $request->supply_address,
                'supply_phone'   => $request->supply_phone,
                'supply_email'   => $request->supply_email,
                'supply_status'  => $request->supply_status,
                'updated_at'     => Carbon::now(),
            ]);

            $userUpdate = [
                'username'     => $request->username,
                'name'         => $request->supply_name,
                'company_code' => $request->company_code,
                'email'        => $request->supply_email,
                'user_phone'   => $request->supply_phone,
                'user_status'  => $request->supply_status,
                'logo_agency'  => $logoPath,
                'updated_at'   => Carbon::now(),
            ];

            if ($request->filled('password')) {
                $userUpdate['password'] = Hash::make($request->password);
            }

            DB::table('users')->where('user_id', $id)->update($userUpdate);

            DB::commit();
            return redirect()->route('staff.supplies.index')->with('success', "แก้ไข Supply \"{$request->supply_name}\" สำเร็จ");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $supply = DB::table('supply_datas')->where('sup_id', $id)->first();
        if (!$supply) {
            return response()->json(['success' => false, 'message' => 'ไม่พบข้อมูล Supply'], 404);
        }

        $vehicleCount = DB::table('vehicles_detail')->where('supply_id', $id)->count();
        if ($vehicleCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "ไม่สามารถลบได้ เนื่องจากมีรถที่ผูกกับ Supply นี้อยู่ {$vehicleCount} คัน",
            ], 422);
        }

        DB::beginTransaction();
        try {
            DB::table('supply_datas')->where('sup_id', $id)->delete();
            DB::table('users')->where('user_id', $id)->delete();

            if ($supply->supply_logo && file_exists(public_path($supply->supply_logo))) {
                unlink(public_path($supply->supply_logo));
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => "ลบ Supply \"{$supply->supply_name}\" สำเร็จ"]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()], 500);
        }
    }
}