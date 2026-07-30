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

class RepairController extends Controller
{


    public function create_repair($record_id)
    {

        if (!in_array(auth()->user()->role, [Role::User, Role::Manager, Role::Agency])) {
            abort(403);
        }

        $record = DB::table('chk_records')
            ->join('vehicles_detail', 'chk_records.veh_id', '=', 'vehicles_detail.car_id')
            ->join('vehicle_types', 'vehicles_detail.car_type', '=', 'vehicle_types.id')
            ->select(
                'vehicles_detail.*',
                'vehicle_types.vehicle_type as veh_type_name',
                'chk_records.created_at as date_check',
                'chk_records.form_id',
                'chk_records.record_id',
                'chk_records.user_id as chk_user',
                'chk_records.agency_id as chk_agent'
            )
            ->where('chk_records.record_id', '=', $record_id)
            ->first();

        $problem_items = DB::table('check_records_result')
            ->join('check_items', 'check_records_result.item_id', '=', 'check_items.id')            
            ->where('check_records_result.record_id', $record_id)
            ->whereIn('check_records_result.result_value', [0, 2])
            ->select(
                'check_items.id as item_id',
                'check_items.item_name',
                'check_records_result.result_value',
                'check_records_result.user_comment'
            )
            ->get();

        $manager_list = DB::table('users')
            ->where('role', 'manager')
            ->where('agency_id', Auth::user()->agency_id)
            ->orderBy('name','asc')
            ->get();

              $images = DB::table('check_result_images')
            ->where('record_id', $record_id)
            ->get()
            ->groupBy('item_id');


        return view('pages.local.CreateRepair', compact('record', 'problem_items','manager_list','images'));
    }
}
