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

class AdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index()
    {
        return view('pages.admin.index');
    }


    public function CompanyList()
    {
        $company_list = DB::table('users')
            ->where('role', '=', 'company')
            ->orderBy('updated_at', 'DESC')
            ->get();

        return view('pages.admin.CompanyList', compact('company_list'));
    }

    public function CompanyCreate()
    {
        $province = DB::table('provinces')
            ->select('id', 'name_th')
            ->orderBy('name_th', 'ASC')
            ->get();


        return view('pages.admin.CompanyCreate', compact('province'));
    }

    public function CompanyEdit($id)
    {
        $province = DB::table('provinces')
            ->select('id', 'name_th')
            ->orderBy('name_th', 'ASC')
            ->get();

        $company_detail = DB::table('users')
            ->join('company_details', 'users.company_code', '=', 'company_details.company_id')
            ->where('users.id', '=', $id)
            ->get();

        $company_logo = DB::table('users')
        ->select('users.logo_agency','users.username')
        ->where('users.id', '=', $id)
        ->first();

        return view('pages.admin.CompanyEdit', compact('province', 'company_detail','company_logo'));
    }


    public function AnnouncementPage()
    {
        $list_post = DB::table('announcements')
            ->join('users', 'announcements.user_id', '=', 'users.id')
            ->select('users.name', 'users.role', 'users.id', 'announcements.id as post_id', 'announcements.title', 'announcements.description', 'announcements.file_upload', 'announcements.updated_at', 'announcements.created_at')
            ->orderBy('announcements.updated_at', 'DESC')
            ->get();

        return view('pages.admin.announce', compact('list_post'));
    }

    public function create_announce()
    {
        return view('pages.admin.create_announce');
    }

    public function insert_post(Request $request)
    {
        $request->validate(
            [
                'title' => 'required|string|max:200',
                'detail' => 'required|string',
                'file_upload' => 'nullable|file|max:5120|mimes:pdf,doc,docx,jpg,jpeg,png',
            ],
            [
                'title.required' => 'กรุณากรอกหัวข้อประกาศ',
                'detail.required' => 'กรุณากรอกเนื้อหาประกาศ',
                'file_upload.mimes' => 'ไฟล์ต้องเป็นประเภท PDF, DOC, DOCX, JPG หรือ PNG เท่านั้น',
                'file_upload.max' => 'ขนาดไฟล์ต้องไม่เกิน 5 MB',
            ]
        );

        $upload_location = 'upload/';

        $fileName = null;

        if ($request->hasFile('file_upload')) {

            $file = $request->file('file_upload');

            $extension = $file->getClientOriginalExtension();
            $newName = Carbon::now()->format('Ymd_His') . '_id' . auth()->id() . '.' . $extension;

            $file->move($upload_location, $newName);

            $fileName = $newName;
        }

        DB::table('announcements')->insert([
            'user_id'    => Auth::user()->id,
            'title'      => $request->input('title'),
            'description' => $request->input('detail'),
            'file_upload' => $fileName,
            'created_at' =>  Carbon::now(),
            'updated_at' =>  Carbon::now(),
        ]);

        return redirect()->route('admin.announce')->with('success', 'บันทึกสำเร็จ');
    }

    public function edit_post($id)
    {
        $announce = DB::table('announcements')
            ->where('id', '=', $id)
            ->first();

        return view('pages.admin.edit_announce', compact('announce'));
    }

    public function update_post(Request $request, $id)
    {

        $request->validate(
            [
                'title' => 'required|string|max:200',
                'detail' => 'required|string',
                'file_upload' => 'nullable|file|max:5120|mimes:pdf,doc,docx,jpg,jpeg,png',
            ],
            [
                'title.required' => 'กรุณากรอกหัวข้อประกาศ',
                'detail.required' => 'กรุณากรอกเนื้อหาประกาศ',
                'file_upload.mimes' => 'ไฟล์ต้องเป็นประเภท PDF, DOC, DOCX, JPG หรือ PNG เท่านั้น',
                'file_upload.max' => 'ขนาดไฟล์ต้องไม่เกิน 5 MB',
            ]
        );

        $post = DB::table('announcements')->where('id', $id)->first();
        if (!$post) {
            abort(404);
        }

        if ($request->hasFile('file_upload')) {

            $file = public_path('upload/' . $post->file_upload);

            if (File::exists($file)) {
                File::delete($file);
            }

            $upload_location = 'upload/';
            $file_new = $request->file_upload;

            $extension = $file_new->getClientOriginalExtension();
            $newName = Carbon::now()->format('Ymd_His') . '_id' . auth()->id() . '.' . $extension;

            $file_new->move($upload_location, $newName);

            $fileName = $newName;
        }

        DB::table('announcements')->where('id', $id)->update([
            'title'      => $request->input('title'),
            'description' => $request->input('detail'),
            'file_upload' => $fileName,
            'updated_at' =>  Carbon::now(),
        ]);

        return redirect()->route('admin.announce')->with('success', 'บันทึกการแก้ไขสำเร็จ');
    }

    public function delete_file($id)
    {
        $post = DB::table('announcements')->where('id', $id)->first();
        if (!$post || !$post->file_upload) {
            return redirect()->back();
        }
        $file = public_path('upload/' . $post->file_upload);

        if (File::exists($file)) {
            File::delete($file);
        }

        DB::table('announcements')->where('id', $id)->update(['file_upload' => null]);

        return redirect()->back()->with('success', 'ลบไฟล์เรียบร้อยแล้ว');
    }

    public function delete_post($id)
    {
        $post = DB::table('announcements')->where('id', $id)->first();

        if ($post->file_upload) {
            $file = public_path('upload/' . $post->file_upload);
            if (File::exists($file)) {
                File::delete($file);
            }
        }

        $delete_post = DB::table('announcements')->where('id', $id)->delete();

        return redirect()->route('admin.announce')->with('success', 'ลบประกาศเรียบร้อยแล้ว');
    }

   
}
