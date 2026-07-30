<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarBrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      
        $brands = [
            'Audi',
            'BMW',
            'Chevrolet',
            'Ford',
            'Honda',
            'Hyundai',
            'Isuzu',
            'Kia',
            'Mazda',
            'Mercedes-Benz',
            'MG',
            'MINI',
            'Mitsubishi',
            'Nissan',
            'Peugeot',
            'Subaru',
            'Suzuki',
            'Toyota',
            'Volkswagen',
            'Volvo',
        ];
 foreach ($brands as $brand) {
            DB::table('car_brands')->insert([
                'brand_name' => $brand
               
            ]);
        }
    }
}
