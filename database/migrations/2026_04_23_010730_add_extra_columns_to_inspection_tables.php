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
     
        Schema::table('check_records_result', function (Blueprint $table) {
            $table->string('result_status', 10)->nullable()
                  ->comment('สถานะการตรวจ (เช่น 1=ผ่าน/ปกติ, 0=ไม่ผ่าน/ไม่ปกติ)')
                  ->after('item_id');               
        });

 
        Schema::table('chk_records', function (Blueprint $table) {
            $table->tinyInteger('evaluate_status')->nullable()
                  ->comment('ผลประเมินรถ: 1=อนุญาตให้ใช้, 2=มีเงื่อนไข/ต้องซ่อม, 3=ไม่อนุญาตให้ใช้')
                  ->after('chk_status'); // ให้อยู่ต่อจาก chk_status
                  
            $table->date('next_inspect_date')->nullable()
                  ->comment('กำหนดระยะเวลาตรวจสภาพใหม่')
                  ->after('evaluate_status');
                  
            $table->string('inspector_sign', 255)->nullable()
                  ->comment('path ภาพลายเซ็นผู้ตรวจ')
                  ->after('next_inspect_date');
                  
            $table->string('driver_sign', 255)->nullable()
                  ->comment('path ภาพลายเซ็นคนขับ/ผู้รับการตรวจ')
                  ->after('inspector_sign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('check_records_result', function (Blueprint $table) {
            $table->dropColumn(['result_status']);
        });

        Schema::table('chk_records', function (Blueprint $table) {
            $table->dropColumn([
                'evaluate_status', 
                'next_inspect_date', 
                'inspector_sign', 
                'driver_sign'
            ]);
        });
    }
};
