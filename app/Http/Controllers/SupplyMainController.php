<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Enums\Role;
use Illuminate\Support\Facades\File;

class SupplyMainController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:supply']);
    }

    public function Vehicles_Create()
    {
         $province = DB::table('provinces')
            ->select('id', 'name_th')
            ->orderBy('name_th', 'ASC')
            ->get();

        $car_brand = DB::table('car_brands')
            ->orderBy('brand_name', 'ASC')
            ->get();

             $car_type = DB::table('vehicle_types')
            ->select('id', 'vehicle_type')
            ->orderBy('vehicle_type', 'ASC')
            ->get();

        return view('pages.supply.CarCreate',compact('province','car_brand','car_type'));
    }

      public function VehiclesInsert(Request $request)
    {
        $supply_id = Auth::user()->user_id;

        $company = DB::table('supply_datas')
        ->where('sup_id',$supply_id)
        ->first();

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

        $vehicleData = [
            'user_id'           => $supply_id,
            'company_code'      => $company->company_code,
            'supply_id'         => $supply_id,
            'car_id'            => $veh_id,
            'car_plate'         => $car_plate,
            'car_brand'         => $request->car_brand,
            'car_model'         => $request->car_model,
            'car_number_record' => $request->car_number_record,
            'car_age'           => $request->car_age,
            'car_mileage'       => $request->car_mileage,
            'car_tax'           => $request->car_tax,
            'car_insure'        => $request->car_insure,
            'car_type'          => $request->car_type,
            'status'            => '1',
            'created_at'        => Carbon::now(),
            'updated_at'        => Carbon::now(),
        ];

        $vehicleDbId = DB::table('vehicles_detail')->insertGetId($vehicleData);

        DB::table('vehicle_activity_logs')->insert([
            'vehicle_id'  => $vehicleDbId,
            'user_id'     => $supply_id,
            'action'      => 'create',
            'before_data' => null,
            'after_data'  => json_encode($vehicleData, JSON_UNESCAPED_UNICODE),
            'created_at'  => Carbon::now(),
        ]);

        return redirect()->route('supply.veh_list')->with('success', 'บันทึกสำเร็จ');
    }

     public function VehiclesList()
    {
        $supply_id = Auth::user()->user_id;
        $veh_list = DB::table('vehicles_detail')
           ->where('supply_id',$supply_id)
            ->get();

        return view('pages.supply.CarList', compact('veh_list'));
    }

        public function InspectorList($id)
    {
        $inst_list = DB::table('inspector_datas')
        ->join('supply_datas','inspector_datas.sup_id','supply_datas.sup_id')
        ->where('inspector_datas.ins_status','=','1')
        ->where('inspector_datas.sup_id','=',$id)
        ->get();

        return view('pages.supply.InspectorList',compact('inst_list'));
    }

        public function Inspector_Create()
    {
        return view('pages.supply.InspectorCreate');
    }

          public function Inspector_Store(Request $request)
    {

        $id = Auth::user()->user_id;
        $ins_id = 'INS-' . Str::upper(Str::random(10));
        $username = str_replace(' ', '', $request->company_user);

        DB::table('inspector_datas')
            ->insert([
                'ins_id'=> $ins_id,
                'sup_id' => $id,
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
                'company_code' => $id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
        ]);

        return redirect()->route('supply.inspector_list', ['id' => $id])->with('success', 'บันทึกสำเร็จ');
    }

    public function chk_list(Request $request)
    {
        $user_id = Auth::user()->user_id;
        $dateFrom = $request->input('date_from');
        $dateTo   = $request->input('date_to');

        $record = DB::table('chk_records')
            ->join('vehicles_detail', 'chk_records.veh_id', '=', 'vehicles_detail.car_id')
            ->join('vehicle_types', 'vehicles_detail.car_type', '=', 'vehicle_types.id')
            ->leftJoin('inspector_datas', 'inspector_datas.ins_id', '=', 'chk_records.user_id')
            ->select(
                'vehicles_detail.*',
                'vehicle_types.vehicle_type as veh_type_name',
                'chk_records.created_at as date_check',
                'chk_records.form_id',
                'chk_records.record_id',
                'chk_records.user_id as chk_user',
                'chk_records.agency_id as chk_agent',
                'inspector_datas.ins_prefix',
                'inspector_datas.ins_name',  
                'inspector_datas.ins_lastname' 
            )
            ->where('chk_records.agency_id', '=', $user_id)
            ->orderBy('chk_records.created_at', 'DESC');  
            
        

        if ($dateFrom && $dateTo) {
            $record->whereBetween('chk_records.created_at', [
                Carbon::parse($dateFrom)->startOfDay(),
                Carbon::parse($dateTo)->endOfDay(),
            ]);
        } elseif ($dateFrom) {
            $record->where('chk_records.created_at', '>=', Carbon::parse($dateFrom)->startOfDay());
        } elseif ($dateTo) {
            $record->where('chk_records.created_at', '<=', Carbon::parse($dateTo)->endOfDay());
        }

        $record_all = $record->get();

        return view('pages.supply.Veh_ChkList', compact('record_all'));
    }

}
