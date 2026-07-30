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
        Schema::create('check_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); //ผู้สร้างข้อตรวจ         
            $table->string('category_id',20); //รหัสหมวดหมู่
            $table->unsignedTinyInteger('item_no'); //ลำดับข้อตรวจ
            $table->string('item_name', 200); //ข้อตรวจ
            $table->text('item_description')->nullable(); //รายละเอียดข้อตรวจ            
            $table->tinyInteger('item_type'); //ประเภทการตรวจ เช่น 1.แบบตัวเลือก 2.แบบพิมพ์ข้อความ
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('check_items');
    }
};
