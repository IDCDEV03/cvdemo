<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vehicles_detail', function (Blueprint $table) {
            $table->string('car_trailer_plate', 200)->nullable()->comment('ทะเบียนหาง');
            $table->date('car_register_date')->nullable()->comment('วันที่จดทะเบียน');
            $table->date('car_insurance_expire')->nullable()->comment('วันที่ประกันหมดอายุ');
            $table->string('car_total_weight', 200)->nullable()->comment('น้ำหนักรถรวม');
            $table->string('car_fuel_type', 200)->nullable()->comment('ชนิดเชื้อเพลิง');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vehicles_detail', function (Blueprint $table) {          
            $table->dropColumn([
                'car_trailer_plate',
                'car_register_date',
                'car_insurance_expire',
                'car_total_weight',
                'car_fuel_type'
            ]);
        });
    }
};
