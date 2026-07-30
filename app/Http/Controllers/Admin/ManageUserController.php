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

class ManageUserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function createAgency()
    {
        return view('pages.admin.create_agency');
    }

    public function insert_agency(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:4',
            'logo' => 'nullable|image|max:10240', // 10MB
        ]);

        $upload_location = 'logo/';

        $fileName = null;

        if ($request->hasFile('logo')) {

            $file = $request->file('logo');
            $extension = $file->getClientOriginalExtension();
            $newName = Carbon::now()->format('Ymd_His') . '_' . auth()->id() . '.' . $extension;
            $file->move($upload_location, $newName);
            $fileName = $upload_location . $newName;
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_phone' => $request->phone,
            'logo_agency' => $fileName,
            'role' => 'agency',
            'created_at' =>  Carbon::now(),
            'updated_at' =>  Carbon::now(),
        ];

        DB::table('users')->insert($data);

        return redirect()->route('admin.agency_list')->with('success', 'สร้างหน่วยงานเรียบร้อยแล้ว');
    }

    public function Agency_list()
    {

        $agencies = DB::table('users')
            ->where('role', 'agency')
            ->orderByDesc('created_at')
            ->get();

        return view('pages.admin.AgencyList', compact('agencies'));
    }

    public function EditAgency($id)
    {
        $agency = DB::table('users')->where('id', $id)->where('role', 'agency')->first();

        if (!$agency) {
            abort(404, 'ไม่พบหน่วยงาน');
        }

        return view('pages.admin.EditAgency', compact('agency'));
    }

    public function UpdateAgency(Request $request, $id)
    {

        $agency = DB::table('users')->where('id', $id)->where('role', 'agency')->first();

        $request->validate([
            'name' => 'required|string|max:200',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:4',
            'logo' => 'nullable|image|max:10048',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'user_phone' => $request->phone,
            'updated_at' => Carbon::now(),
        ];

        // ถ้ากรอก password ใหม่
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('logo')) {
            $file = public_path('logo/' . $agency->logo_agency);

            if (File::exists($file)) {
                File::delete($file);
            }
            $upload_location = 'logo/';
            $file_new = $request->logo;

            $extension = $file_new->getClientOriginalExtension();
            $newName = Carbon::now()->format('Ymd_His') . '_' . auth()->id() . '.' . $extension;

            $file_new->move($upload_location, $newName);

            $data['logo_agency'] = 'logo/'.$newName;
        }

        DB::table('users')->where('id', $id)->update($data);

        return redirect()->route('admin.agency_list')->with('success', 'แก้ไขข้อมูลหน่วยงานเรียบร้อย');
    }

    public function DestroyAgency($id)
    {
        $agency = DB::table('users')->where('id', $id)->where('role', 'agency')->first();

        if (!$agency) {
            abort(404, 'ไม่พบหน่วยงาน');
        }

        //ลบโลโก้
        if ($agency->logo_agency) {
            $file = public_path($agency->logo_agency);
            if (File::exists($file)) {
                File::delete($file);
            }
        }

        DB::table('users')->where('id', $id)->delete();

        return redirect()->route('admin.agency_list')->with('success', 'ลบหน่วยงานเรียบร้อย');
    }

    public function AgencyDetail($id)
    {
        $agency = DB::table('users')->where('id', $id)->first();

        if (!$agency) {
            abort(404, 'ไม่พบหน่วยงาน');
        }

        $managers = DB::table('users')
            ->where('agency_id', $agency->id)
            ->where('role', 'manager')
            ->get();

        $users = DB::table('users')
            ->where('agency_id', $agency->id)
            ->where('role', 'user')
            ->get();

        return view('pages.admin.AgencyDetail', compact('agency', 'managers', 'users'));
    }

    //CRUD Member
    public function createMember($role, $id)
    {

        $agency = DB::table('users')->where('id', $id)->first();

        if (!in_array($role, ['manager', 'user'])) {
            abort(400, 'Invalid role');
        }

        return view('pages.admin.create_member', compact('role', 'agency'));
    }

    public function insertMember(Request $request)
    {

        $agency = $request->agency_id;

        $request->validate([
            'name' => 'required|string|max:200',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:4',
            'role' => 'required|in:manager,user',
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

        return redirect()->route('admin.agency.show', $agency)->with('success', 'เพิ่มผู้ใช้งานเรียบร้อย');
    }


    public function destroyMember($id)
    {
        $member = DB::table('users')->where('id', $id)->first();
        
        DB::table('users')->where('id', $id)->delete();

        return redirect()->route('admin.agency.show', $member->agency_id)->with('success', 'ลบผู้ใช้งานเรียบร้อย');
    }
}
