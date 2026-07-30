<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Enums\Role;

class InsuranceController extends Controller
{
    private function onlyStaff()
    {
        if (Auth::user()->role->value !== 'staff') {
            abort(403);
        }
    }

    public function index()
    {
        $this->onlyStaff();
        $companies = DB::table('insurance_companies')->orderBy('name')->get();
        return view('pages.staff.insurance.index', compact('companies'));
    }

    public function create()
    {
        $this->onlyStaff();
        return view('pages.staff.insurance.create');
    }

    public function store(Request $request)
    {
        $this->onlyStaff();
        $request->validate([
            'name' => 'required|string|max:100|unique:insurance_companies,name',
        ], [
            'name.required' => 'กรุณากรอกชื่อบริษัทประกันภัย',
            'name.unique'   => 'ชื่อบริษัทนี้มีอยู่ในระบบแล้ว',
        ]);

        DB::table('insurance_companies')->insert([
            'name'       => trim($request->name),
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('staff.insurance.index')
            ->with('success', 'เพิ่มบริษัทประกันภัย "' . trim($request->name) . '" เรียบร้อยแล้ว');
    }

    public function edit($id)
    {
        $this->onlyStaff();
        $company = DB::table('insurance_companies')->find($id);
        if (!$company) abort(404);
        return view('pages.staff.insurance.edit', compact('company'));
    }

    public function update(Request $request, $id)
    {
        $this->onlyStaff();
        $request->validate([
            'name' => 'required|string|max:100|unique:insurance_companies,name,' . $id,
        ], [
            'name.required' => 'กรุณากรอกชื่อบริษัทประกันภัย',
            'name.unique'   => 'ชื่อบริษัทนี้มีอยู่ในระบบแล้ว',
        ]);

        DB::table('insurance_companies')->where('id', $id)->update([
            'name'       => trim($request->name),
            'is_active'  => $request->has('is_active') ? 1 : 0,
            'updated_at' => now(),
        ]);

        return redirect()->route('staff.insurance.index')
            ->with('success', 'อัปเดตข้อมูลบริษัทประกันภัยเรียบร้อยแล้ว');
    }

    public function destroy($id)
    {
        $this->onlyStaff();
        $company = DB::table('insurance_companies')->find($id);
        if (!$company) abort(404);

        DB::table('insurance_companies')->where('id', $id)->delete();

        return redirect()->route('staff.insurance.index')
            ->with('success', 'ลบบริษัทประกันภัย "' . $company->name . '" เรียบร้อยแล้ว');
    }
}