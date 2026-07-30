<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class AgentCompanyController extends Controller
{
    public function index(Request $request)
    {
        $keyword = trim($request->keyword);

        $query = DB::table('company_details')
            ->where('agency_id', Auth::user()->user_id);

        if (!empty($keyword)) {
            $query->where(function ($q) use ($keyword) {
                $q->where('company_id', 'like', "%{$keyword}%")
                    ->orWhere('company_name', 'like', "%{$keyword}%")
                    ->orWhere('company_province', 'like', "%{$keyword}%");
            });
        }

        $companies = $query
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('pages.agency.Companies_index', compact('companies', 'keyword'));
    }

    public function create()
    {
        $province = DB::table('provinces')
            ->select('id', 'name_th')
            ->orderBy('name_th', 'ASC')
            ->get();


        return view('pages.agency.Companies_create', compact('province'));
    }

    public function store(Request $request)
    {
        $agent_id = Auth::user()->user_id;
        $request->validate([
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5048',
            'company_name' => 'required|string|max:200',
            'company_address' => 'nullable|string',
            'company_province' => 'nullable|string|max:30',
            'form_limit' => 'nullable|integer|min:0',
            'start_date' => 'nullable|date',
            'expire_date' => 'nullable|date|after_or_equal:start_date',
        ], [
            'company_logo.image' => 'ไฟล์ที่อัพโหลดต้องเป็นรูปภาพเท่านั้น',
            'company_logo.mimes' => 'รองรับไฟล์ jpeg, png, jpg, gif เท่านั้น',
            'company_logo.max' => 'ขนาดไฟล์โลโก้ต้องไม่เกิน 5MB',
            'company_name.required' => 'กรุณากรอกชื่อบริษัท',
            'company_name.max' => 'ชื่อบริษัทต้องไม่เกิน 200 ตัวอักษร',
            'company_province.max' => 'จังหวัดต้องไม่เกิน 30 ตัวอักษร',
            'form_limit.integer' => 'จำนวนฟอร์มต้องเป็นตัวเลข',
            'form_limit.min' => 'จำนวนฟอร์มต้องไม่น้อยกว่า 0',
            'expire_date.after_or_equal' => 'วันหมดอายุต้องไม่น้อยกว่าวันเริ่มใช้งาน',
        ]);

        // ป้องกัน company_id ซ้ำแบบง่าย
        do {
            $companyId = 'COM-' . strtoupper(Str::random(8));
            $exists = DB::table('company_details')->where('company_id', $companyId)->exists();
        } while ($exists);

        // 2. จัดการอัพโหลดไฟล์โลโก้
        $logoPath = null; // กำหนดค่าเริ่มต้นกรณีไม่อัพโหลด

        if ($request->hasFile('company_logo')) {
            $file = $request->file('company_logo');
            $filename = $companyId . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('logo'), $filename);
            $logoPath = 'logo/' . $filename;
        }

        DB::table('company_details')->insert([
            'user_created_id' => $agent_id,
            'agency_id' => $agent_id,
            'company_id' => $companyId,
            'company_logo' => $logoPath,
            'company_name' => $request->company_name,
            'company_address' => $request->company_address,
            'company_province' => $request->company_province,
            'form_limit' => $request->form_limit ?? 0,
            'require_user_approval' => $request->has('require_user_approval') ? 1 : 0,
            'start_date' => $request->start_date,
            'expire_date' => $request->expire_date,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('users')->insert([
            'user_id' => $companyId,
            'username' => $request->company_user,
            'prefix' => '-',
            'name' => $request->company_name,
            'lastname' => '-',
            'user_status' => $request->has('require_user_approval') ? 1 : 0,
            'email' => $request->company_email,
            'password' => Hash::make($request->company_password),
            'user_phone' => $request->company_phone,
            'logo_agency' => $logoPath,
            'role' => 'company',
            'company_code' => $companyId,
            'agency_user_id' => $agent_id,
            'agency_id' => $agent_id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);


        return redirect()
            ->route('companies.index')
            ->with('success', 'สร้างบริษัทเรียบร้อยแล้ว');
    }

    public function edit($id)
    {

        $province = DB::table('provinces')
            ->select('id', 'name_th')
            ->orderBy('name_th', 'ASC')
            ->get();

        $company = DB::table('company_details')
            ->join('users', 'company_details.company_id', 'users.user_id')
            ->select(
                'company_details.*',
                'users.user_id',
                'users.user_phone',
                'users.email',
                'users.username',
            )
            ->where('company_details.company_id', $id)
            ->where('company_details.agency_id', Auth::user()->user_id)
            ->first();

        if (!$company) {
            abort(404);
        }

        return view('pages.agency.companies_edit', compact('province', 'company'));
    }

    public function update(Request $request, $id)
    {

        $request->validate([
            'company_name' => 'required|string|max:200',
            'company_address' => 'nullable|string',
            'company_province' => 'nullable|string|max:30',
            'form_limit' => 'nullable|integer|min:0',
            'start_date' => 'nullable|date',
            'expire_date' => 'nullable|date|after_or_equal:start_date',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'company_user' => 'required|string',
            'company_password' => 'nullable|string',
        ]);


        // 1. ค้นหาข้อมูลบริษัทเดิมจาก ID
        $company = DB::table('company_details')->where('company_id', $id)->first();

        if (!$company) {
            return redirect()->back()->with('error', 'ไม่พบข้อมูลบริษัท');
        }

        $companyId = $company->company_id;
        $logoPath = $company->company_logo;

        // 2. จัดการรูปภาพ (ถ้ามีการอัปโหลดรูปใหม่มาทับ)
        if ($request->hasFile('company_logo')) {
            if ($logoPath && File::exists(public_path($logoPath))) {
                File::delete(public_path($logoPath));
            }

            // อัปโหลดรูปใหม่
            $file = $request->file('company_logo');
            $filename = $companyId . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('logo'), $filename);
            $logoPath = 'logo/' . $filename;
        }

        // 3. เริ่มการทำ Transaction
        DB::beginTransaction();

        try {
            // อัปเดตข้อมูลลงตาราง company_details
            DB::table('company_details')->where('company_id', $id)->update([
                'company_logo' => $logoPath,
                'company_name' => $request->company_name,
                'company_address' => $request->company_address,
                'company_province' => $request->company_province,
                'form_limit' => $request->form_limit ?? 0,
                'require_user_approval' => $request->has('require_user_approval') ? 1 : 0,
                'start_date' => $request->start_date,
                'expire_date' => $request->expire_date,
                'updated_at' => Carbon::now(),
            ]);

            // เตรียมข้อมูลสำหรับอัปเดตลงตาราง users
            $userData = [
                'username' => $request->company_user,
                'name' => $request->company_name, // อัปเดตชื่อให้ตรงกับชื่อบริษัท
                'user_status' => $request->has('require_user_approval') ? 1 : 0,
                'email' => $request->company_email,
                'user_phone' => $request->company_phone,
                'logo_agency' => $logoPath, // อัปเดตโลโก้ให้ด้วย
                'updated_at' => Carbon::now(),
            ];

            // ตรวจสอบว่ามีการพิมพ์ Password ใหม่มาหรือไม่ (ถ้าไม่พิมพ์มาก็ใช้รหัสเดิม)
            if ($request->filled('company_password')) {
                $userData['password'] = Hash::make($request->company_password);
            }

            // อัปเดตข้อมูลตาราง users โดยอ้างอิงจาก user_id ที่ตรงกับ company_id
            DB::table('users')->where('user_id', $companyId)->update($userData);
            DB::commit();

            return redirect()
                ->route('companies.index')
                ->with('success', 'อัปเดตข้อมูลบริษัทเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'เกิดข้อผิดพลาดในการอัปเดตข้อมูล: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
    
        $agent_id = Auth::user()->user_id;

        // 1. ค้นหาบริษัท (เช็คด้วย company_id เหมือนตอนอัปเดต และเช็คสิทธิ์ให้ลบได้เฉพาะของ Agency ตัวเอง)
        $company = DB::table('company_details')
            ->where('company_id', $id)
            ->where('agency_id', $agent_id)
            ->first();

        if (!$company) {
            // ใช้ redirect กลับไปพร้อมแจ้งเตือน แทนการทำ abort(404) เพื่อให้ UX ดูเป็นมิตรขึ้น
            return redirect()->route('companies.index')->with('error', 'ไม่พบข้อมูลบริษัท หรือคุณไม่มีสิทธิ์ลบข้อมูลนี้');
        }

        // 2. เริ่ม Transaction ป้องกันข้อมูลลบไม่ครบ
        DB::beginTransaction();

        try {
            // 3. ลบไฟล์โลโก้ในโฟลเดอร์ public/logo (ถ้ามี)
            if ($company->company_logo && File::exists(public_path($company->company_logo))) {
                File::delete(public_path($company->company_logo));
            }

            // 4. ลบข้อมูลบัญชีผู้ใช้ (User) ของบริษัทนี้ทิ้งด้วย
            DB::table('users')
                ->where('user_id', $company->company_id)
                ->delete();

            // 5. ลบข้อมูลในตาราง company_details
            DB::table('company_details')
                ->where('company_id', $company->company_id)
                ->delete();

            // ยืนยันการลบข้อมูล
            DB::commit();

            return redirect()
                ->route('companies.index')
                ->with('success', 'ลบข้อมูลบริษัทเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'เกิดข้อผิดพลาดในการลบข้อมูล: ' . $e->getMessage());
        }
    }
}
