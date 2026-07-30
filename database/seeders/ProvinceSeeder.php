<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         
        $json = File::get(database_path('seeders/data/provinces.json'));
        
        $provinces = json_decode($json);

        foreach ($provinces as $item) {
            DB::table('provinces')->insert([
                'id' => $item->id,
                'code' => $item->code,
                'name_th' => $item->name_th,
                'name_en' => $item->name_en,
                'geography_id' => $item->geography_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
    }
}
}