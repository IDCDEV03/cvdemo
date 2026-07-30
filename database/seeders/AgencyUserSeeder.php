<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class AgencyUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        $agencies = [
            'ศูนย์อบรมเทรนนิ่งเซนเตอร์',
            'บริษัท ไอดี ไดรฟ์ จำกัด',
            'บริษัท ปูนซีเมนต์ จำกัด',
            'บริษัท ทดสอบระบบ จำกัด',
            'บริษัท เอบีซี จำกัด',
        ];

        foreach ($agencies as $index => $agencyName) {       
            $username = 'Test' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);

            $id = DB::table('users')->insertGetId([           
                'username'   => $username,
                'prefix'     => '',
                'name'       => $agencyName,
                'lastname'  => '',
                'email'      => $faker->unique()->safeEmail,
                'password'   => Hash::make($username),
                'role'       => 'agency',                
                'created_at' => now(),
                'updated_at' => now(),
            ]);

              DB::table('users')->where('id', $id)->update([
                'agency_id' => $id,
            ]);
        }

      
    
    }
}
