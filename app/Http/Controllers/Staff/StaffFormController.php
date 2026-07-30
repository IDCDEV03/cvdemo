<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Enums\Role;
use Illuminate\Support\Facades\File;

class StaffFormController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:staff']);
    }

    public function FormList()
    {
        $form_list = DB::table('forms')
            ->orderBy('updated_at', 'DESC')
            ->get();

        return view('pages.staff.FormList', compact('form_list'));
    }

    public function FormNew()
    {
        $car_type = DB::table('vehicle_types')->get();

        $supply_list = DB::table('supply_datas')
            ->where('supply_status', '1')
            ->orderBy('supply_name', 'ASC')
            ->get();



        return view('pages.staff.FormNew', compact('car_type', 'supply_list'));
    }

    public function FormStore(Request $request)
    {
        // 1. Validation (ตรวจสอบข้อมูลเบื้องต้น)
        $request->validate([
            'form_name'  => 'required|string|max:255',
            'form_scope' => 'required|in:public,specific',
            'supply_ids' => 'required_if:form_scope,specific|array'
        ]);

        $form_id = Str::upper(Str::random(8));

        // เริ่มการทำ Transaction ป้องกันเซฟข้อมูลไม่ครบ
        DB::beginTransaction();

        try {
            // 2. บันทึกข้อมูล Form (เขียนครั้งเดียว ไม่ต้องแยก if-else)
            DB::table('forms')->insert([
                'user_id'       => Auth::user()->user_id,
                'form_id'       => $form_id,
                'form_code'     => $request->input('form_code'),
                'form_name'     => $request->form_name,
                'form_status'   => $request->form_setting,
                'form_open'     => $request->form_scope,
                'car_type'      => $request->vehicle_type_id,
                'created_at'    => Carbon::now(),
                'updated_at'    => Carbon::now()
            ]);

            // 3. จัดการสิทธิ์กรณีเลือกแบบ เฉพาะบางหน่วยงาน (specific)
            if ($request->form_scope === 'specific' && $request->has('supply_ids')) {
                $permissionData = [];

                // นำข้อมูลเข้า Array เพื่อเตรียมบันทึกพร้อมกัน
                foreach ($request->supply_ids as $supId) {
                    $permissionData[] = [
                        'form_id'           => $form_id,
                        'sup_id'            => $supId,
                        'permission_status' => $request->form_setting,
                        'created_at'        => Carbon::now(),
                        'updated_at'        => Carbon::now(),
                    ];
                }

                // Batch Insert: Query ครั้งเดียวจบ เร็วขึ้นมาก
                if (!empty($permissionData)) {
                    DB::table('form_permission')->insert($permissionData);
                }
            }

            // ยืนยันการบันทึกทั้งหมด
            DB::commit();

            return redirect()->route('staff.form_new2', ['id' => $form_id, 'order' => '1'])
                ->with('success', 'บันทึกข้อมูลสำเร็จ');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage());
        }
    }
    public function FormNew_Step2($id, $order)
    {
        $chk_cates = DB::table('forms')
            ->where('form_id', '=', $id)
            ->first();

        return view('pages.staff.FormCreate2', ['id' => $id, 'order' => $order], compact('chk_cates'));
    }

    public function FormStep2_Insert(Request $request, $id)
    {
        // 1. ตรวจสอบข้อมูลก่อนทำอะไรต่อ
        $request->validate([
            'chk_cats_name' => 'required|array',
            'chk_cats_name.*' => 'required|string|max:255',
        ]);

        $uid = Auth::user()->user_id;
        $insertData = []; // เตรียมไว้ทำ Batch Insert

        DB::beginTransaction();

        try {
            foreach ($request->chk_cats_name as $index => $name) {
                $random_id = Str::upper(Str::random(8));
                $order_no = $request->order_no[$index] ?? ($index + 1);

                // เตรียม Array ข้อมูล
                $insertData[] = [
                    'user_id'       => $uid,
                    'form_id'       => $id,
                    'cates_no'      => $order_no,
                    'category_id'   => 'CAT-' . ($index + 1) . '-' . $random_id,
                    'chk_cats_name' => $name,
                    'chk_detail'    => $request->chk_detail[$index] ?? null,
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now()
                ];
            }

            if (!empty($insertData)) {
                DB::table('check_categories')->insert($insertData);
            }

            DB::commit();

            return redirect()->route('staff.form_step3', ['id' => $id])
                ->with('success', 'บันทึกหมวดหมู่เรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    public function FormNew_Step3($id)
    {

        $data = DB::table('check_categories')
            ->where('form_id', '=', $id)
            ->orderBy('cates_no', 'ASC')
            ->get();

        $form_name = DB::table('forms')
            ->where('form_id', '=', $id)
            ->first();

        return view('pages.staff.FormCreate3', ['id' => $id], compact('data', 'form_name'));
    }

    public function categories_detail($cates_id)
    {
        $cates_data = DB::table('check_categories')
            ->join('forms', 'check_categories.form_id', '=', 'forms.form_id')
            ->select('forms.form_name', 'forms.form_id', 'check_categories.category_id', 'check_categories.chk_cats_name')
            ->where('check_categories.category_id', '=', $cates_id)
            ->first();

        $item_data = DB::table('check_items')
            ->where('category_id', '=', $cates_id)
            ->get();

        return view('pages.staff.Categories_Detail', ['cates_id' => $cates_id], compact('cates_data', 'item_data'));
    }

    //แก้ไขชื่อหมวดหมู่
    public function cates_update(Request $request, $id)
    {
        $request->validate([
            'category_name' => 'required|string|max:255',
        ]);

        $form_id = DB::table('check_categories')
            ->select('check_categories.form_id')
            ->where('id', $id)
            ->first();

        DB::table('check_categories')
            ->where('id', $id)
            ->update([
                'chk_cats_name' => $request->category_name,
                'updated_at' => now(),
            ]);

        return redirect()->route('staff.form_step3', ['id' => $form_id->form_id])->with('success', 'แก้ไขหมวดหมู่เรียบร้อยแล้ว');
    }

    //ลบหมวดหมู่
    public function cates_delete(Request $request, $id)
    {
        $form_id = $request->form_id;
        // ลบ check_items ที่อ้างอิงหมวดหมู่
        DB::table('check_items')->where('category_id', $id)->delete();

        // ลบหมวดหมู่
        DB::table('check_categories')->where('category_id', $id)->delete();

        return redirect()->route('staff.form_step3', ['id' => $form_id])->with('success', 'ลบหมวดหมู่และข้อตรวจที่เกี่ยวข้องเรียบร้อยแล้ว');
    }


    public function item_create($id)
    {
        $cates_data = DB::table('check_categories')
            ->join('forms', 'check_categories.form_id', '=', 'forms.form_id')
            ->select('forms.form_name', 'check_categories.category_id', 'check_categories.chk_cats_name')
            ->where('check_categories.category_id', '=', $id)
            ->first();

        return view('pages.staff.ItemCreate', ['id' => $id], compact('cates_data'));
    }

    public function item_create_plus($id)
    {
        $cates_data = DB::table('check_categories')
            ->join('forms', 'check_categories.form_id', '=', 'forms.form_id')
            ->select('forms.form_name', 'check_categories.category_id', 'check_categories.chk_cats_name')
            ->where('check_categories.category_id', '=', $id)
            ->first();

        $item_data = DB::table('check_items')
            ->select('item_no', 'item_name')
            ->where('category_id', $id)
            ->get();

        $lastOrder = DB::table('check_items')
            ->where('category_id', $id)
            ->max('item_no');

        $item_types = DB::table('item_types')
            ->get();

        $lastOrder = $lastOrder ?? 0;

        $category = DB::table('check_categories')->where('id', $id)->first();

        return view('pages.staff.ItemCreate_plus', ['id' => $id], compact('cates_data', 'lastOrder', 'item_data', 'item_types'));
    }

    // เพิ่มข้อตรวจใหม่ยังบันทึกแบบนับ 1 ใหม่ ยังไม่ได้แก้ไข //
    public function item_insert(Request $request)
    {
        // 1. ตรวจสอบความถูกต้องของข้อมูลเบื้องต้น
        $request->validate([
            'cate_id'      => 'required',
            'item_name'    => 'required|array|min:1',
            'item_name.*'  => 'required|string',
            'item_type'    => 'required|array',
            'item_image.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120' // จำกัดไฟล์รูปไม่เกิน 5MB
        ], [
            'item_name.*.required' => 'กรุณากรอกชื่อข้อตรวจให้ครบถ้วน',
            'item_image.*.image'   => 'ไฟล์ที่อัปโหลดต้องเป็นรูปภาพเท่านั้น'
        ]);

        $uid = Auth::user()->user_id;
        $insertData = [];
        $uploadedFiles = []; // เก็บ Path ไฟล์ไว้ เผื่อกรณี DB พัง จะได้ตามลบทิ้งได้

        // 2. เริ่ม Transaction
        DB::beginTransaction();

        try {
            foreach ($request->item_name as $index => $name) {
                $fileName = null;
                $list = $index + 1;
                $item_id = 'CHK_' . $list . '_' . Str::upper(Str::random(8));

                // 3. จัดการอัปโหลดไฟล์ภาพ
                if ($request->hasFile('item_image.' . $index)) {
                    $file = $request->file('item_image.' . $index);
                    $extension = $file->getClientOriginalExtension();
                    $newName = 'item_' . $item_id . '.' . $extension;

                    // ใช้ public_path() เพื่อป้องกันปัญหา Path ผิดเพี้ยนบน Server
                    $uploadPath = public_path('uploads/items');
                    // ถ้ายังไม่มีโฟลเดอร์ให้สร้างใหม่
                    if (!file_exists($uploadPath)) {
                        mkdir($uploadPath, 0755, true);
                    }
                    $file->move($uploadPath, $newName);
                    // Path ที่จะเก็บลง DB
                    $fileName = 'uploads/items/' . $newName;
                    // เก็บ Path เต็มไว้สำหรับลบกรณีเกิด Error
                    $uploadedFiles[] = $uploadPath . '/' . $newName;
                }

                // 4. นำข้อมูลใส่ Array รอ Insert พร้อมกัน
                $insertData[] = [
                    'user_id'          => $uid,
                    'category_id'      => $request->cate_id,
                    'item_id'          => $item_id,
                    'item_no'          => $list,
                    'item_name'        => $name,
                    'item_description' => $request->item_description[$index] ?? null,
                    'item_type'        => $request->item_type[$index] ?? null,
                    'item_image'       => $fileName,
                    'created_at'       => Carbon::now(),
                    'updated_at'       => Carbon::now(),
                ];
            }

            if (!empty($insertData)) {
                DB::table('check_items')->insert($insertData);
            }

            DB::commit();
            return redirect()->route('staff.categories_detail', ['cates_id' => $request->cate_id])
                ->with('success', 'บันทึกข้อตรวจเรียบร้อยแล้ว');
        } catch (\Exception $e) {

            DB::rollBack();
            foreach ($uploadedFiles as $filePath) {
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            return redirect()->back()
                ->withInput()
                ->with('error', 'เกิดข้อผิดพลาดในการบันทึก: ' . $e->getMessage());
        }
    }


    public function item_edit($id)
    {

        $item_data = DB::table('check_items')
            ->join('check_categories', 'check_items.category_id', '=', 'check_categories.category_id')
            ->select('check_categories.chk_cats_name', 'check_items.item_no', 'check_items.item_name', 'item_description', 'check_items.item_type', 'check_items.item_image')
            ->where('check_items.item_id', '=', $id)
            ->first();

        return view('pages.staff.ItemEdit', compact('item_data'));
    }


    public function item_delete_image($id)
    {
        $post = DB::table('check_items')->where('item_id', $id)->first();
        if (!$post || !$post->item_image) {
            return redirect()->back();
        }
        $file = public_path('upload/' . $post->item_image);

        if (File::exists($file)) {
            File::delete($file);
        }

        DB::table('check_items')->where('item_id', $id)->update(['item_image' => null]);

        return redirect()->back()->with('success', 'ลบไฟล์เรียบร้อยแล้ว');
    }

    public function item_update(Request $request)
    {

        $id = $request->item_id;

        $item = DB::table('check_items')->where('item_id', $id)->first();
        if (!$item) {
            return redirect()->back()->with('error', 'ไม่พบข้อมูลข้อตรวจ');
        }

        $imagePath = $item->item_image;
        $item_random_id = Str::upper(Str::random(8));
        $fileName = null;

        if ($request->hasFile('item_image')) {

            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
            $imagePath_1 = 'upload/';
            $file = $request->file('item_image');

            $extension = $file->getClientOriginalExtension();
            $newName = 'item_' . $item->item_no . '_' . $item_random_id . '.' . $extension;
            $file->move($imagePath_1, $newName);
            $fileName = $imagePath_1 . $newName;
        }

        DB::table('check_items')->where('item_id', $id)->update([
            'item_name' => $request->item_name,
            'item_description' => $request->item_description,
            'item_type' => $request->item_type,
            'item_image' => $fileName ?? null,
            'updated_at' => Carbon::now(),
        ]);

        return redirect()->route('staff.categories_detail', ['cates_id' => $item->category_id])->with('success', 'อัปเดตข้อมูลสำเร็จ');
    }

    public function PreviewForm($form_id)
    {
        // 1. ดึงข้อมูลฟอร์มหลัก
        $form = DB::table('forms')
            ->leftJoin('vehicle_types', 'forms.car_type', '=', 'vehicle_types.id')
            ->where('forms.form_id', $form_id)
            ->select('forms.*', 'vehicle_types.vehicle_type')
            ->first();

        if (!$form) {
            return redirect()->back()->with('error', 'ไม่พบข้อมูลฟอร์ม');
        }

        // 2. ดึงหมวดหมู่ (Categories) ทั้งหมดของฟอร์มนี้ และเรียงตามลำดับ (cates_no)
        $categories = DB::table('check_categories')
            ->where('form_id', $form_id)
            ->orderBy('cates_no', 'asc')
            ->get();

        // 3. ดึงข้อตรวจ (Items) ทั้งหมดที่อยู่ในหมวดหมู่เหล่านั้น 
        // เทคนิค: ใช้ pluck ดึง category_id มาทำ whereIn จะ Query แค่ครั้งเดียว
        $categoryIds = $categories->pluck('category_id')->toArray();

        $itemsGrouped = DB::table('check_items')
            ->whereIn('category_id', $categoryIds)
            ->orderBy('item_no', 'asc')
            ->get()
            ->groupBy('category_id'); // จัดกลุ่มข้อตรวจตาม category_id

        // ส่งข้อมูลไปที่ Blade
        return view('pages.staff.Preview_Form', compact('form', 'categories', 'itemsGrouped'));
    }
}
