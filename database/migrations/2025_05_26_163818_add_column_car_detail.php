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
        Schema::table('check_records', function (Blueprint $table) {
            $table->string('plate', 20)->after('record_id');
            $table->string('province', 50)->after('plate');
            $table->string('vehicle_type', 50)->after('province');
            $table->date('tax_exp')->after('vehicle_type');
            $table->string('vehicle_image', 200)->after('tax_exp'); //ภาพรถที่ใช้ตรวจ
           });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('check_records', function (Blueprint $table) {
            //
        });
    }
};
