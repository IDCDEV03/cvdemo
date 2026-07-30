<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ItemTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
          $types = [
            'แบบตัวเลือก (ผ่าน/ไม่ผ่าน)',
            'แบบตัวเลือก (ปกติ/ปรับปรุง)',
            'แบบกรอกข้อความ',
            'แบบเลือกวันที่',
        ];

        foreach ($types as $type) {
            DB::table('item_types')->insert([
                'item_type_name' => $type,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
