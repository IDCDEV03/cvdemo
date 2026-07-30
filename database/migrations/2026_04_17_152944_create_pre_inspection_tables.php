<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // ตารางเก็บชื่อแม่แบบ
        Schema::create('pre_inspection_templates', function (Blueprint $table) {
            $table->id();
            $table->string('company_id')->nullable(); // อ้างอิง ID ของบริษัท
            $table->string('template_name');
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });

   
        Schema::create('pre_inspection_fields', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('template_id');
            $table->string('field_label'); // เช่น "ถ่ายรูปรอบคัน"
            $table->string('field_type'); // text, image, gps
            $table->boolean('is_required')->default(1);
            $table->integer('sort_order')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pre_inspection_fields');
        Schema::dropIfExists('pre_inspection_templates');
    }
};