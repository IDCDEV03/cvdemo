<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class ManageCompanyController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function CompanyStore(Request $request)
    {

        $user_gen = DB::table('users')->where('id', Auth::id())->first();

        //เช็ค username ซ้ำ 
        $usernameExists = DB::table('users')->where('username', $request->company_user)->exists();

        if ($usernameExists) {
            return back()
                ->withInput()
                ->with('error', 'Username นี้มีอยู่แล้ว กรุณาเลือกชื่ออื่น');
        }

        $comp_id = 'CP-' . Str::upper(Str::random(9));        
        $upload_location = 'logo/';

        $fileName = null;

        if ($request->hasFile('company_logo')) {
            $file = $request->file('company_logo');
            $extension = $file->getClientOriginalExtension();
            $newName = Carbon::now()->format('Ymd_His') . '_' . $comp_id . '.' . $extension;
            $file->move($upload_location, $newName);
            $fileName = $upload_location.$newName;
        }

        DB::table('company_details')->insert([
            'user_created_id' => $user_gen->user_id,
            'agency_id' => '5',
            'company_id' => $comp_id,
            'company_logo' => $fileName,
            'company_name' => $request->company_name,
            'company_address' => $request->company_address,
            'company_province' => $request->company_province,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);


        DB::table('users')->insert([
            'user_id' => $comp_id,
            'username' => $request->company_user,
            'prefix' => '-',
            'name' => $request->company_name,
            'lastname' => '-',
            'user_status' => '1',
            'email' => $request->company_email,
            'password' => Hash::make($request->company_password),
            'user_phone' => $request->company_phone,
            'role' => 'company',
            'company_code' => $comp_id,
            'agency_user_id' => '5',
            'logo_agency' => $fileName,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return redirect()->route('admin.cp_list')->with('success', 'บันทึกสำเร็จ');
    }

    public function UpdateStatus($id, $status)
    {
        if ($status == '2') {
            DB::table('users')
                ->where('id', $id)
                ->update([
                    'user_status'      => '2',
                    'updated_at' =>  Carbon::now(),
                ]);
            return redirect()->route('admin.cp_list')->with('success', 'บันทึกสำเร็จ');
        } elseif ($status == '1') {
            DB::table('users')
                ->where('id', $id)
                ->update([
                    'user_status'      => '1',
                    'updated_at' =>  Carbon::now(),
                ]);
            return redirect()->route('admin.cp_list')->with('success', 'บันทึกสำเร็จ');
        }
    }

    public function CompanyUpdate(Request $request, $id, $tab)
    {
        $company_id = DB::table('users')->where('id', $id)->first();

        if ($tab == 'part1') {

            DB::table('company_details')
                ->where('company_id', $company_id->user_id)
                ->update([
                    'company_name' => $request->company_name,
                    'company_address' => $request->company_address,
                    'company_province' => $request->province,
                    'updated_at' => Carbon::now(),
                ]);

            DB::table('users')
                ->where('user_id', $company_id->user_id)
                ->update([
                    'name' => $request->company_name,
                    'email' => $request->company_email,
                    'user_phone' => $request->company_phone,
                    'updated_at' => Carbon::now(),
                ]);
            return redirect()->route('admin.cp_list')->with('success', 'บันทึกการแก้ไขสำเร็จ');
        } elseif ($tab == 'part2') {

            $usernameExists = DB::table('users')->where('username', $request->company_user)->exists();

            if ($usernameExists) {
                return back()
                    ->withInput()
                    ->withErrors(['company_username' => 'Username นี้มีอยู่แล้ว กรุณาเลือกชื่ออื่น']);
            }

            DB::table('users')
                ->where('user_id', $company_id->user_id)
                ->update([
                    'username' => $request->company_user,
                    'updated_at' => Carbon::now(),
                ]);
            return redirect()->route('admin.cp_list')->with('success', 'บันทึกการแก้ไขสำเร็จ');
        } elseif ($tab == 'part3') {
            DB::table('users')
                ->where('user_id', $company_id->company_code)
                ->update([
                    'password' => Hash::make($request->company_password),
                    'updated_at' => Carbon::now(),
                ]);
            return redirect()->route('admin.cp_list')->with('success', 'บันทึกการแก้ไขสำเร็จ');
        } elseif ($tab == 'part4') {

              // ดึงข้อมูลเก่า
        $old = DB::table('users')->where('user_id', $company_id->user_id)->first();

        $newFileName = $old->logo_agency; // ค่าเดิมก่อนเปลี่ยน
        $upload_location = 'logo/';

        if ($request->hasFile('logo_agency')) {
            // ลบไฟล์เก่าออก
            if ($old && $old->logo_agency && Storage::exists('public/'.$old->logo_agency)) {
                Storage::delete('public/'.$old->logo_agency);
            }

            // อัปโหลดใหม่
            $file = $request->file('logo_agency');
            $newFileName = $upload_location.$company_id->user_id.'_'.time().'_'.$file->getClientOriginalName();
            $file->move($upload_location, $newFileName);           
        }

        // อัปเดต DB_user
        DB::table('users')
            ->where('user_id', $company_id->user_id)
            ->update([
                'logo_agency' => $newFileName,
                'updated_at' => now(),
            ]);

                 // อัปเดต DB_company_details
        DB::table('company_details')
            ->where('company_id', $company_id->user_id)
            ->update([
                'company_logo' => $newFileName,
                'updated_at' => now(),
            ]);
   return redirect()->route('admin.cp_list')->with('success', 'บันทึกการแก้ไขสำเร็จ');
        }
    }

    public function SupplyAll()
    {
        $supply_list = DB::table('users')
        ->select('users.*','company_details.company_name')
        ->join('company_details','users.company_code','=','company_details.company_id')
        ->where('users.role','supply')
        ->orderBy('users.name','ASC')
        ->get();

         return view('pages.admin.SupplyAll', compact('supply_list'));
    }

    public function SupplyEdit($id)
    {
        $supply_data = DB::table('users')
        ->join('supply_datas','users.user_id','=','supply_datas.sup_id')  
        ->where('supply_datas.sup_id',$id)     
        ->first();

        $company_list = DB::table('users')
        ->where('role','company')
        ->where('user_status','1')
        ->orderBy('name','ASC')
        ->get();
        
        return view('pages.admin.SupplyEdit',['id'=>$id], compact('supply_data','company_list'));
    }
 
      public function SupplyUpdate(Request $request, $id, $tab)
    {
        $supply_id = DB::table('users')->where('user_id', $id)->first();

        if ($tab == 'part1') {

            DB::table('supply_datas')
                ->where('sup_id', $supply_id->user_id)
                ->update([
                    'supply_name' => $request->supply_name,
                    'supply_address' => $request->supply_address,
                    'supply_phone' => $request->supply_phone,
                    'company_code' => $request->company_code,
                    'supply_email' => $request->supply_email,
                    'updated_at' => Carbon::now(),
                ]);

            DB::table('users')
                ->where('user_id', $supply_id->user_id)
                ->update([
                    'name' => $request->supply_name,
                    'email' => $request->supply_email,
                    'user_phone' => $request->supply_phone,
                    'company_code' => $request->company_code,
                    'updated_at' => Carbon::now(),
                ]);
            return redirect()->route('admin.supply_all')->with('success', 'บันทึกการแก้ไขสำเร็จ');
        } elseif ($tab == 'part2') {

            $usernameExists = DB::table('users')->where('username', $request->company_user)->exists();

            if ($usernameExists) {
                return back()
                    ->withInput()
                    ->withErrors(['company_username' => 'Username นี้มีอยู่แล้ว กรุณาเลือกชื่ออื่น']);
            }

            DB::table('users')
                ->where('user_id', $supply_id->user_id)
                ->update([
                    'username' => $request->company_user,
                    'updated_at' => Carbon::now(),
                ]);
            return redirect()->route('admin.supply_all')->with('success', 'บันทึกการแก้ไขสำเร็จ');
        } elseif ($tab == 'part3') {
            DB::table('users')
                ->where('user_id', $supply_id->user_id)
                ->update([
                    'password' => Hash::make($request->supply_password),
                    'updated_at' => Carbon::now(),
                ]);
            return redirect()->route('admin.supply_all')->with('success', 'บันทึกการแก้ไขสำเร็จ');
        }
    }

    public function SupList($id)
    {
       $company_name = DB::table('users')
        ->where('company_code','=',$id)
        ->first();

        $supply_list = DB::table('supply_datas')
        ->where('company_code','=',$id)
        ->get();

    return view('pages.admin.SupplyList', compact('company_name','supply_list'));
    }

    public function SupCreate($id = null)
    {
        $companies = DB::table('company_details')
            ->select('company_id', 'company_name')
            ->orderBy('company_name')
            ->get();

        return view('pages.admin.SupplyCreate', compact('companies', 'id'));
    }

    public function SupInsert(Request $request)
    {
        $request->validate([
            'company_code'    => 'required',
            'supply_name'     => 'required|string|max:200',
            'supply_address'  => 'nullable|string',
            'supply_phone'    => 'nullable|string|max:50',
            'supply_email'    => 'nullable|email|max:50',
            'supply_status'   => 'required|in:0,1',
            'company_user'    => 'required|string|max:30|unique:users,username',
            'supply_password' => 'required|string|min:4',
            'supply_logo'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'company_code.required'    => 'กรุณาเลือกบริษัทฯว่าจ้าง',
            'supply_name.required'     => 'กรุณากรอกชื่อ Supply',
            'company_user.required'    => 'กรุณากรอก Username',
            'company_user.unique'      => 'Username นี้ถูกใช้งานแล้ว',
            'supply_password.required' => 'กรุณากรอกรหัสผ่าน',
            'supply_password.min'      => 'รหัสผ่านต้องมีอย่างน้อย 4 ตัวอักษร',
        ]);

        $sup_id = 'SUP-' . Str::upper(Str::random(10));

        $upload_location = 'logo/';

        $fileName = null;

        if ($request->hasFile('supply_logo')) {
            $file = $request->file('supply_logo');
            $extension = $file->getClientOriginalExtension();
            $newName = Carbon::now()->format('Ymd_His') . '_' . $sup_id . '.' . $extension;
            $file->move($upload_location, $newName);
            $fileName = $upload_location.$newName;
        }

        DB::beginTransaction();
        try {
            DB::table('supply_datas')->insert([
                'company_code' => $request->company_code,
                'sup_id' => $sup_id,
                'supply_name' => $request->supply_name,
                'supply_logo' => $fileName,
                'supply_address' => $request->supply_address,
                'supply_phone' => $request->supply_phone,
                'supply_email' => $request->supply_email,
                'supply_status' => $request->supply_status,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            DB::table('users')->insert([
                'user_id' => $sup_id,
                'username' => $request->company_user,
                'prefix' => '-',
                'name' => $request->supply_name,
                'lastname' => '-',
                'user_status' => $request->supply_status,
                'email' => $request->supply_email,
                'password' => Hash::make($request->supply_password),
                'user_phone' => $request->supply_phone,
                'role' => 'supply',
                'company_code' => $request->company_code,
                'agency_user_id' => '5',
                'logo_agency' => $fileName,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            DB::commit();

            return redirect()->route('admin.supply_all')
                ->with('success', "เพิ่ม Supply \"{$request->supply_name}\" สำเร็จ");
        } catch (\Exception $e) {
            DB::rollBack();

            if ($fileName && file_exists($fileName)) {
                unlink($fileName);
            }

            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage())->withInput();
        }
    }



}
