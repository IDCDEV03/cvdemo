<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class CompanyProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:company']);
    }

    public function show()
    {
        $user = DB::table('users')->where('user_id', Auth::user()->user_id)->first();
        return view('pages.company.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $userId = Auth::user()->user_id;

        $request->validate([
            'username' => 'required|string|max:30|unique:users,username,' . $userId . ',user_id',
            'email'    => 'nullable|email|max:200|unique:users,email,' . $userId . ',user_id',
            'password' => 'nullable|string|min:4|confirmed',
        ], [
            'username.required' => 'กรุณากรอกชื่อผู้ใช้',
            'username.unique'   => 'ชื่อผู้ใช้นี้ถูกใช้งานแล้ว',
            'email.unique'      => 'อีเมลนี้ถูกใช้งานแล้ว',
            'password.min'      => 'รหัสผ่านต้องมีอย่างน้อย 4 ตัวอักษร',
            'password.confirmed' => 'รหัสผ่านไม่ตรงกัน',
        ]);

        $update = [
            'username'   => $request->username,
            'email'      => $request->email,
            'updated_at' => Carbon::now(),
        ];

        if ($request->filled('password')) {
            $update['password'] = Hash::make($request->password);
        }

        DB::table('users')->where('user_id', $userId)->update($update);

        return redirect()->route('company.profile')->with('success', 'อัปเดตข้อมูลสำเร็จ');
    }
}