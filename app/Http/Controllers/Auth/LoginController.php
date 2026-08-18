<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Enums\Role;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showRegisterForm()
    {
        $agencies = DB::table('users')
            ->where('role', 'agency')
            ->orderBy('name', 'ASC')
            ->get();
        return view('auth.register', compact('agencies'));
    }

    public function register_store(Request $request)
    {
        if (empty($request->prefix)) {
            return redirect()->back()->with('error', 'กรุณาเลือกคำนำหน้า');
        }

        if (empty($request->agency_id)) {
            return redirect()->back()->with('error', 'กรุณาเลือกหน่วยงาน');
        }

        DB::table('users')->insert([
            'prefix' => $request->prefix,
            'name' => $request->first_name,
            'lastname' => $request->lastname,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'agency_user_id' => $request->agency_id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return redirect('/')->with('success', 'สมัครสมาชิกเรียบร้อยแล้ว');
    }

    public function login(Request $request)
    {
       $credentials = $request->validate([
        'username' => ['required', 'string'],
        'password' => ['required'],
    ]);

        // Check if account is suspended before attempting login
        $existingUser = DB::table('users')->where('username', $credentials['username'])->first();
        if ($existingUser && Hash::check($credentials['password'], $existingUser->password) && $existingUser->user_status == '0') {
            return back()->withErrors([
                'username' => 'บัญชีไม่สามารถใช้งานได้ กรุณาติดต่อเจ้าหน้าที่',
            ])->onlyInput('username');
        }

        if (Auth::attempt(array_merge($credentials, ['user_status' => '1']), $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
        
            return match ($user->role) {
                Role::Admin => redirect()->route('admin.dashboard'),
                Role::Manager => redirect()->route('manager.dashboard'),
                Role::Agency => redirect()->route('agency.index'),
                Role::User => redirect()->route('local.home'),
                Role::Company => redirect()->route('company.index'),
                Role::Staff => redirect()->route('staff.index'),
                Role::Supply => redirect()->route('supply.home'),
                Role::Inspector => redirect()->route('ins-dashboard'),
                default => redirect('/home'),
            };
        }

        return back()->withErrors([
            'username' => 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง',
        ])->onlyInput('username');
    }
    
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->withHeaders([
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
        ]);
    }

    public function check_username(Request $request)
    {
         $exists = DB::table('users')->where('username', $request->username)->exists();
    return response()->json(['exists' => $exists]);
    }
}
