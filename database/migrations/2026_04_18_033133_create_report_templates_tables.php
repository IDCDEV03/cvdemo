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
       // 1. ตารางเก็บแม่แบบรายงานหลัก (เก็บ HTML หัวและท้าย)
        Schema::create('report_templates', function (Blueprint $table) {
            $table->id();
            $table->string('company_id')->nullable();
            $table->string('template_name');
            $table->text('header_html')->nullable(); // เก็บโค้ด HTML ที่มี Shortcode
            $table->text('footer_html')->nullable(); // เก็บโค้ด HTML ที่มี Shortcode
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });

        // 2. ตารางเก็บฟิลด์กำหนดเอง (Custom Fields) สำหรับแม่แบบนี้
        Schema::create('report_template_fields', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('template_id');
            $table->string('field_label'); // เช่น "อายุคนขับ", "ประสบการณ์"
            $table->string('field_key');   // รหัสตัวแปร เช่น "driver_age" (นำไปใช้เป็น [driver_age])
            $table->string('field_type');  // text, number, date
            $table->boolean('is_required')->default(1);
            $table->integer('sort_order')->default(1);
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
       Schema::dropIfExists('report_template_fields');
        Schema::dropIfExists('report_templates');
    }
};
