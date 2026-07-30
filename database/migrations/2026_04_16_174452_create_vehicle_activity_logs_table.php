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
        Schema::create('vehicle_activity_logs', function (Blueprint $table) {
            $table->id();           
            $table->unsignedBigInteger('vehicle_id')->comment('ID ของรถที่ถูกแก้ไข');          
            $table->string('user_id', 20)->comment('รหัสผู้ที่เข้ามาแก้ไข');            
            $table->string('action', 20)->comment('ประเภทการทำงาน เช่น update, create, delete');
            $table->json('before_data')->nullable()->comment('ข้อมูลก่อนการแก้ไข');
            $table->json('after_data')->nullable()->comment('ข้อมูลหลังการแก้ไข');
            $table->timestamps(); 
            $table->foreign('vehicle_id')->references('id')->on('vehicles_detail')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicle_activity_logs');
    }
};
