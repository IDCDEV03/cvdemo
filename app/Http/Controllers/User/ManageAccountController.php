<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Enums\Role;
use Illuminate\Support\Facades\File;

class ManageAccountController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:agency']);
    }

    public function createAccount($role)
    {
        $id = Auth::id();

        $agency = DB::table('users')->where('id', $id)->first();

        if (!in_array($role, ['manager', 'user'])) {
            abort(400, 'Invalid role');
        }

        return view('pages.agency.create_user', compact('role', 'agency'));
    }

    public function checkUsername(Request $request)
    {
        $exists = DB::table('users')
            ->where('username', $request->username)
            ->exists();

        return response()->json(['exists' => $exists]);
    }

    public function InsertAccount(Request $request)
    {
        $agency = Auth::id();

        $request->validate([
            'name' => 'required|string|max:200',
            'username' => 'required|unique:users,username',
            'password' => 'required|min:4',
            'avatar' => 'nullable|image|max:5048',
            'signature' => 'nullable|image|max:5048',
        ]);

        $avatarPath = null;
        $signaturePath = null;

        if ($request->hasFile('avatar')) {
            $avatar_name = Str::upper(Str::random(8));
            $file = $request->file('avatar');
            $filename = $avatar_name . '.' . $file->getClientOriginalExtension();

            $file->move(public_path('fileupload'), $filename);
            $avatarPath = 'fileupload/' . $filename;
        }


        if ($request->hasFile('signature')) {
            $sign_name = Str::upper(Str::random(10));
            $file = $request->file('signature');
            $filename = $sign_name . '.' . $file->getClientOriginalExtension();

            $file->move(public_path('fileupload'), $filename);
            $signaturePath = 'fileupload/' . $filename;
        }

        DB::table('users')->insert([
            'username' => $request->username,
            'prefix' => $request->prefix,
            'name' => $request->name,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_phone' => $request->phone,
            'role' => $request->role,
            'agency_id' => $agency,
            'profile_image' => $avatarPath,
            'signature_image' => $signaturePath,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $role = $request->role;

        if($role == 'manager')
        {
        return redirect()->route('agency.manager_list')->with('success', 'เพิ่มผู้ใช้งานเรียบร้อย');
        }elseif($role == 'user')
        {
        return redirect()->route('agency.user_list')->with('success', 'เพิ่มผู้ใช้งานเรียบร้อย');
        }
    }

    public function ManagerList()
    {
        $agency_id = Auth::id();
        $manager = DB::table('users')->where('agency_id', $agency_id)
            ->where('role', 'manager')
            ->get();

        return view('pages.agency.ManagerList', compact('manager'));
    }

    public function UserList()
    {
        $agency_id = Auth::id();
        $user_list = DB::table('users')->where('agency_id', $agency_id)
            ->where('role', 'user')
            ->get();

        return view('pages.agency.UserList', compact('user_list'));
    }
}
