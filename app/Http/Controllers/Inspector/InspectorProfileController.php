<?php

namespace App\Http\Controllers\Inspector;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class InspectorProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:inspector']);
    }

    public function show()
    {
        $userId = Auth::user()->user_id;

        $user = DB::table('users')->where('user_id', $userId)->first();
        $inspector = DB::table('inspector_datas')->where('ins_id', $userId)->first();

        $currentYearBE = Carbon::now()->year + 543;
        $age = null;
        if ($inspector && $inspector->ins_birthyear) {
            $age = $currentYearBE - (int) $inspector->ins_birthyear;
        }

        return view('pages.inspector.profile', compact('user', 'inspector', 'age'));
    }

    public function update(Request $request)
    {
        $userId = Auth::user()->user_id;

        $request->validate([
            'username'       => 'required|string|max:30|unique:users,username,' . $userId . ',user_id',
            'email'          => 'nullable|email|max:200|unique:users,email,' . $userId . ',user_id',
            'prefix'         => 'nullable|string|max:20',
            'name'           => 'required|string|max:50',
            'lastname'       => 'nullable|string|max:50',
            'user_phone'     => 'nullable|string|max:20',
            'ins_birthyear'  => 'nullable|digits:4',
            'dl_number'      => 'nullable|string|max:50|unique:inspector_datas,dl_number,' . DB::table('inspector_datas')->where('ins_id', $userId)->value('id'),
            'password'       => 'nullable|string|min:6|confirmed',
            'signature_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'username.unique'      => 'ชื่อผู้ใช้นี้ถูกใช้งานแล้ว',
            'email.unique'         => 'อีเมลนี้ถูกใช้งานแล้ว',
            'password.min'         => 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร',
            'password.confirmed'   => 'รหัสผ่านไม่ตรงกัน',
            'ins_birthyear.digits' => 'ปีเกิดต้องเป็นตัวเลข 4 หลัก (พ.ศ.)',
            'signature_image.image' => 'ไฟล์ต้องเป็นรูปภาพเท่านั้น',
            'signature_image.max'   => 'ขนาดไฟล์ต้องไม่เกิน 2MB',
        ]);

        DB::beginTransaction();
        try {
            $userUpdate = [
                'username'   => $request->username,
                'prefix'     => $request->prefix,
                'name'       => $request->name,
                'lastname'   => $request->lastname,
                'email'      => $request->email,
                'user_phone' => $request->user_phone,
                'updated_at' => Carbon::now(),
            ];

            if ($request->filled('password')) {
                $userUpdate['password'] = Hash::make($request->password);
            }

            if ($request->hasFile('signature_image')) {
                $oldUser = DB::table('users')->where('user_id', $userId)->first();
                if ($oldUser->signature_image && file_exists(public_path($oldUser->signature_image))) {
                    unlink(public_path($oldUser->signature_image));
                }

                $file = $request->file('signature_image');
                $filename = 'SIG_' . $userId . '_' . date('YmdHis') . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('fileupload'), $filename);
                $userUpdate['signature_image'] = 'fileupload/' . $filename;
            }

            DB::table('users')->where('user_id', $userId)->update($userUpdate);

            DB::table('inspector_datas')->where('ins_id', $userId)->update([
                'ins_prefix'    => $request->prefix,
                'ins_name'      => $request->name,
                'ins_lastname'  => $request->lastname,
                'ins_phone'     => $request->user_phone,
                'ins_birthyear' => $request->ins_birthyear,
                'updated_at'    => Carbon::now(),
            ]);

            DB::commit();
            return redirect()->route('ins.profile')->with('success', 'อัปเดตข้อมูลสำเร็จ');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }
}
