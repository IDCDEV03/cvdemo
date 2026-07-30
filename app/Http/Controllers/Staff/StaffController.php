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

class StaffController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:staff,company']);
    }

    public function VehiclesList()
    {
        $veh_list = DB::table('vehicles_detail')
            ->select('vehicles_detail.car_id', 'vehicles_detail.car_plate', 'vehicles_detail.car_brand', 'vehicles_detail.car_model', 'users.company_code', 'users.name', 'vehicle_types.vehicle_type', 'vehicles_detail.status', 'vehicles_detail.created_at','supply_datas.supply_name')
            ->leftjoin('users', 'users.company_code', '=', 'vehicles_detail.company_code')
            ->leftjoin('supply_datas', 'vehicles_detail.supply_id', 'supply_datas.sup_id')
            ->leftjoin('vehicle_types', 'vehicles_detail.car_type', 'vehicle_types.id')
            ->orderBy('vehicles_detail.id', 'ASC')
            ->groupBy('vehicles_detail.car_id')
            ->get();

        return view('pages.staff.VehiclesList', compact('veh_list'));
    }

    public function VehiclesRegister()
    {

        $car_type = DB::table('vehicle_types')
            ->select('id', 'vehicle_type')
            ->orderBy('id', 'ASC')
            ->get();

        $province = DB::table('provinces')
            ->select('id', 'name_th')
            ->orderBy('name_th', 'ASC')
            ->get();

        $car_brand = DB::table('car_brands')
            ->orderBy('brand_name', 'ASC')
            ->get();

        $company_list = DB::table('users')
            ->where('role', '=', 'company')
            ->where('user_status', '=', '1')
            ->get();

        $supply_data = DB::table('supply_datas')
            ->orderBy('supply_name', 'ASC')
            ->get();

        return view('pages.staff.VehiclesRegister', compact('car_type', 'province', 'car_brand', 'company_list', 'supply_data'));
    }

    public function getSupplyByCompany(Request $request)
    {
       
        $supplies = DB::table('supply_datas')
            ->where('company_code', $request->company_id)
            ->orderBy('supply_name','ASC')
            ->pluck('supply_name', 'sup_id');
            

        return response()->json($supplies);
    }

    public function VehiclesInsert(Request $request)
    {
        $staff_id = Auth::id();

        if (empty($request->province)) {
            return redirect()->back()->with('error', 'กรุณาเลือกทะเบียนจังหวัด');
        }

        if (empty($request->car_brand)) {
            return redirect()->back()->with('error', 'กรุณาเลือกยี่ห้อรถ');
        }

        $rawInput = $request->input('plate');
        $cleanPlate = str_replace(' ', '', $rawInput); //ตัดช่องว่างออก

        $car_plate = $cleanPlate . " " . $request->province;
        $veh_id = 'VEH-' . Str::upper(Str::random(9));

        DB::table('vehicles_detail')
            ->insert([
                'user_id' => $staff_id,
                'company_code' => $request->company_id,
                'supply_id' => $request->supply_id,
                'car_id' => $veh_id,
                'car_plate' => $car_plate,
                'car_brand' => $request->car_brand,
                'car_model' => $request->car_model,
                'car_number_record' => $request->car_number_record,
                'car_age' => $request->car_age,
                'car_mileage' => $request->car_mileage,
                'car_tax' => $request->car_tax,
                'car_insure' => $request->car_insure,
                'car_type' => $request->car_type,
                'status' => '1',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

        return redirect()->route('staff.veh_list')->with('success', 'บันทึกสำเร็จ');
    }

    public function CompanyList()
    {
        $company_list = DB::table('users')           
            ->where('role', '=', 'company')
            ->orderBy('updated_at', 'DESC')
            ->get();

         return view('pages.staff.CompanyList',compact('company_list'));
    }

    public function SupList()
    {
       $supply_name = DB::table('users')
       ->join('company_details','users.company_code','=','company_details.company_id')
        ->where('users.role','=','supply')
        ->get();

    return view('pages.staff.SupplyList', compact('supply_name'));
    }

    public function Company_SupList($id)
    {
       $company_name = DB::table('users')
        ->where('company_code','=',$id)
        ->first();

        $supply_name = DB::table('supply_datas')
        ->where('company_code','=',$id)
        ->get();

    return view('pages.staff.Cp_Sup_List', compact('company_name','supply_name'));
    }

    public function InspectorList()
    {
        $inst_list = DB::table('inspector_datas')
        ->join('supply_datas','inspector_datas.sup_id','supply_datas.sup_id')
        ->where('inspector_datas.ins_status','=','1')
        ->get();

        return view('pages.staff.InspectorList',compact('inst_list'));
    }

    public function Inspector_Create()
    {
          $supply_list = DB::table('supply_datas')           
            ->where('supply_status', '=', '1')
            ->orderBy('updated_at', 'DESC')
            ->get();

        return view('pages.staff.Inspector_Create',compact('supply_list'));
    }

    public function Inspector_Store(Request $request)
    {        

        if ($request->supply_id == '0') {
            return redirect()->back()->with('error', 'กรุณาเลือก Supply');
        }

        $username = str_replace(' ', '', $request->company_user);
        $ins_id = 'INS-' . Str::upper(Str::random(10));
        

        DB::table('inspector_datas')
            ->insert([
                'ins_id'=> $ins_id,
                'sup_id' => $request->supply_id,
                'ins_prefix' => $request->prefix,
                'ins_name'=> $request->name,
                'ins_lastname' => $request->lastname,
                'dl_number' => $request->dl_number,
                'ins_phone'=>$request->ins_phone,
                'ins_birthyear'=>$request->ins_birthyear,
                'ins_experience'=>$request->ins_experience,
                'ins_status' => '1',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

        DB::table('users')
        ->insert([
                'user_id'=>$ins_id,
                'username'=>$request->company_user,
                'prefix'=>$request->prefix,
                'name'=>$request->name,
                'lastname'=> $request->lastname,
                'user_status'=>'1',
                'password'=>Hash::make($request->inspector_password),
                'role'=>'user',
                'company_code' => $request->supply_id,
                 'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
        ]);

        return redirect()->route('staff.inspector_list')->with('success', 'บันทึกสำเร็จ');
    }
}
