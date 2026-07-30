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

class InspectorController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:staff']);
    }

    // 1. หน้าแสดงรายชื่อช่างทั้งหมด
    public function index()
    {
        
      $inspectors = DB::table('inspector_datas')
        ->leftJoin('company_details', 'inspector_datas.company_code', '=', 'company_details.company_id')    
        ->leftJoin('supply_datas', 'inspector_datas.sup_id', '=', 'supply_datas.sup_id')   
        ->select(
            'inspector_datas.*', 
            'company_details.company_name', 
            'supply_datas.supply_name'
        )
        ->orderBy('inspector_datas.created_at', 'desc')
        ->get();
        return view('pages.staff.Inspector.index', compact('inspectors'));
    }

    // 2. หน้าฟอร์มเพิ่มช่างใหม่
    public function create()
    {

        $companies = DB::table('company_details')->where('require_user_approval', '1')->get();
        $supplies = DB::table('supply_datas')->where('supply_status', '1')
            ->orderBy('supply_name', 'ASC')
            ->get();

        return view('pages.staff.Inspector.create', compact('companies', 'supplies'));
    }

    // 3. บันทึกข้อมูลช่างลงฐานข้อมูล
    public function store(Request $request)
    {

      $request->validate([
            'company_code' => 'required',
            'name' => 'required', 
            'inspector_type' => 'required',
            'company_user' => 'required',
            'inspector_password' => 'required',
        ]);

        $companyCode = $request->company_code;

        $insId = 'INS-' . strtoupper(Str::random(9));
        $username = str_replace(' ', '', $request->company_user);

        DB::beginTransaction(); 
        try {
           
            DB::table('users')->insert([
                'user_id' => $insId,
                'username' => $username,
                'prefix' => $request->prefix,
                'name' => $request->name,
                'lastname' => $request->lastname,
                'user_status' => '1',
                'password' => Hash::make($request->inspector_password),
                'role' => 'inspector',
                'company_code' => $companyCode,
                'supply_user_id' => ($request->inspector_type == '2') ? $request->sup_id : null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // ส่วนที่ 2: บันทึกลงตาราง inspector_datas
            DB::table('inspector_datas')->insert([
                'ins_id' => $insId,
                'company_code' => $companyCode,
                'sup_id' => ($request->inspector_type == '2') ? $request->sup_id : null,
                'ins_prefix' => $request->prefix ?? '-',
                'ins_name' => $request->name,
                'ins_lastname' => $request->lastname ?? '-',
                'dl_number' => $request->dl_number,
                'ins_phone'=>$request->ins_phone,
                'ins_birthyear'=>$request->ins_birthyear,
                'ins_experience'=>$request->ins_experience,
                'inspector_type' => $request->inspector_type,
                'ins_status' => '1',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // ส่วนที่ 3: จัดการสิทธิ์ของช่าง Outsource (Type 3)
            if ($request->inspector_type == '3' && $request->has('outsource_supplies')) {
                $accessData = [];
                foreach ($request->outsource_supplies as $supId) {
                    $accessData[] = [
                        'ins_id' => $insId,
                        'supply_id' => $supId,
                        'assigned_by' => Auth::user()->user_id, // Staff คนที่เพิ่ม
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                }
                // บันทึกสิทธิ์หลายๆ Supply ลงตาราง inspector_supply_access ทีเดียว
                DB::table('inspector_supply_access')->insert($accessData);
            }

            DB::commit(); // ยืนยันการบันทึกข้อมูลทั้งหมด
            return redirect()->route('staff.inspectors.index')->with('success', 'สร้างบัญชีใหม่สำเร็จ!');
        } catch (\Exception $e) {
            DB::rollBack(); // ถ้ายกเลิก หรือพังกลางคัน ให้ย้อนข้อมูลกลับ ไม่ให้เซฟลง DB
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    // 4. Edit form - pre-filled with existing inspector data
    public function edit($id)
    {
        $inspector = DB::table('inspector_datas')->where('id', $id)->first();
        if (!$inspector) {
            return redirect()->route('staff.inspectors.index')->with('error', 'ไม่พบข้อมูลช่าง');
        }

        $user = DB::table('users')->where('user_id', $inspector->ins_id)->first();
        $companies = DB::table('company_details')->where('require_user_approval', '1')->get();
        $supplies = DB::table('supply_datas')->where('supply_status', '1')
            ->orderBy('supply_name', 'ASC')
            ->get();

        // Current outsource supply access list (for type 3)
        $outsourceAccess = DB::table('inspector_supply_access')
            ->where('ins_id', $inspector->ins_id)
            ->pluck('supply_id')
            ->toArray();

        return view('pages.staff.Inspector.edit', compact('inspector', 'user', 'companies', 'supplies', 'outsourceAccess'));
    }

    // 5. Update inspector data
    public function update(Request $request, $id)
    {
        $inspector = DB::table('inspector_datas')->where('id', $id)->first();
        if (!$inspector) {
            return redirect()->route('staff.inspectors.index')->with('error', 'ไม่พบข้อมูลช่าง');
        }

        $request->validate([
            'company_code'    => 'required',
            'name'            => 'required',
            'inspector_type'  => 'required',
        ]);

        DB::beginTransaction();
        try {
            $supId = ($request->inspector_type == '2') ? $request->sup_id : null;

            DB::table('inspector_datas')->where('id', $id)->update([
                'company_code'    => $request->company_code,
                'sup_id'          => $supId,
                'ins_prefix'      => $request->prefix ?? '-',
                'ins_name'        => $request->name,
                'ins_lastname'    => $request->lastname ?? '-',
                'dl_number'       => $request->dl_number,
                'ins_phone'       => $request->ins_phone,
                'ins_birthyear'   => $request->ins_birthyear,
                'ins_experience'  => $request->ins_experience,
                'inspector_type'  => $request->inspector_type,
                'ins_status'      => $request->ins_status ?? '1',
                'updated_at'      => Carbon::now(),
            ]);

            $userData = [
                'prefix'          => $request->prefix,
                'name'            => $request->name,
                'lastname'        => $request->lastname,
                'company_code'    => $request->company_code,
                'supply_user_id'  => $supId,
                'updated_at'      => Carbon::now(),
            ];

            if ($request->filled('inspector_password')) {
                $userData['password'] = Hash::make($request->inspector_password);
            }

            DB::table('users')->where('user_id', $inspector->ins_id)->update($userData);

            // Sync outsource supply access for type 3
            DB::table('inspector_supply_access')->where('ins_id', $inspector->ins_id)->delete();
            if ($request->inspector_type == '3' && $request->has('outsource_supplies')) {
                $accessData = [];
                foreach ($request->outsource_supplies as $supplyId) {
                    $accessData[] = [
                        'ins_id'      => $inspector->ins_id,
                        'supply_id'   => $supplyId,
                        'assigned_by' => Auth::user()->user_id,
                        'created_at'  => Carbon::now(),
                        'updated_at'  => Carbon::now(),
                    ];
                }
                DB::table('inspector_supply_access')->insert($accessData);
            }

            DB::commit();
            return redirect()->route('staff.inspectors.index')->with('success', 'อัปเดตข้อมูลช่างสำเร็จ!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    // 6. Deactivate inspector (soft delete by status)
    public function destroy($id)
    {
        $inspector = DB::table('inspector_datas')->where('id', $id)->first();
        if ($inspector) {
            DB::table('inspector_datas')->where('id', $id)->update(['ins_status' => '0']);
            DB::table('users')->where('user_id', $inspector->ins_id)->update(['user_status' => 0]);
        }
        return back()->with('success', 'ปิดการใช้งานช่างสำเร็จ');
    }
}
