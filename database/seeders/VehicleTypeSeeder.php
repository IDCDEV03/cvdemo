<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VehicleTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            'รถยนต์',
            'รถตู้',
            'รถจักรยานยนต์',
            'รถกระบะ',
            'รถบรรทุก',
        ];

        foreach ($types as $type) {
            DB::table('vehicle_types')->insert([
                'vehicle_type' => $type,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
