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
        Schema::create('drivers_detail', function (Blueprint $table) {
           $table->id();
            $table->string('driver_id', 30)->unique()->comment('รหัสพนักงานขับรถ เช่น DRV-0001');
            $table->string('company_id', 50)->nullable()->comment('รหัสบริษัทต้นสังกัด');
            $table->string('supply_id', 50)->nullable()->comment('รหัส Supply ต้นสังกัด');
            
            // ข้อมูลส่วนตัว
            $table->string('prefix', 50)->comment('คำนำหน้า');
            $table->string('name', 100)->comment('ชื่อ');
            $table->string('lastname', 100)->comment('นามสกุล');
            $table->string('id_card_no', 20)->nullable()->comment('เลขประจำตัวประชาชน');
            $table->string('phone', 20)->nullable()->comment('เบอร์โทรศัพท์');
            
            // ข้อมูลการขับขี่
            $table->string('driver_license_no', 50)->nullable()->comment('เลขที่ใบอนุญาตขับขี่');
            $table->date('license_expire_date')->nullable()->comment('วันที่ใบขับขี่หมดอายุ');
            $table->string('assigned_car_id', 50)->nullable()->comment('รหัสรถประจำตัวที่ขับ (เชื่อมกับ vehicles_detail)');
            $table->date('hire_date')->nullable()->comment('วันที่บรรจุ/เริ่มงาน');
            
            $table->tinyInteger('driver_status')->default(1)->comment('สถานะ: 1=ปกติ, 2=ลาออก/พักงาน');
            $table->text('remark')->nullable()->comment('ข้อมูลอื่นๆ เพิ่มเติม');
            
            // ผู้บันทึก และ Timestamps
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();
            $table->softDeletes(); // ใช้ Soft Delete เผื่อกู้คืนข้อมูล
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('drivers_detail');
    }
};
