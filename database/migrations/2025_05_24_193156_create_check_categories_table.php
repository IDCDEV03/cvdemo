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
        Schema::create('check_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); //ผู้สร้าง categories
            $table->string('form_id',20); //ใช้กับฟอร์มอะไร
            $table->foreign('form_id')->references('form_id')->on('forms')->onDelete('cascade');
            $table->string('category_id',20); //รหัสหมวดหมู่
            $table->string('chk_cats_name', 200); // ชื่อหมวดหมู่ 
            $table->text('chk_detail')->nullable(); // คำอธิบายหมวดหมู่
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
        Schema::dropIfExists('check_categories');
    }
};
