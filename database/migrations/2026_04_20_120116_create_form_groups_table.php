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
        Schema::create('form_groups', function (Blueprint $table) {
            $table->id();
            
            // --- ข้อมูลพื้นฐาน ---
            $table->string('form_group_name',200)->comment('ชื่อกลุ่มฟอร์ม/แพ็กเกจ');
            $table->text('description')->nullable()->comment('รายละเอียดเพิ่มเติม');

            // --- Multi-tenancy & System Default ---
            // ถ้าเป็น true แสดงว่าเป็นฟอร์มมาตรฐานที่ Admin ระบบสร้างให้ทุกบริษัทใช้
            $table->boolean('is_system_default')->default(false)->index();
            // เก็บ ID ของบริษัท (เป็น null ได้หากเป็น is_system_default)
            $table->unsignedBigInteger('company_id')->nullable()->index();

            // --- Components (Foreign Keys) ---
            // ส่วนประกอบ 3 ชิ้นที่นำมาประกอบกัน (ตามกฎคือห้ามแก้หลังจากบันทึกแล้ว)
            $table->unsignedBigInteger('pre_inspection_template_id')->nullable();
            $table->string('check_item_form_id',30)->nullable();
            $table->unsignedBigInteger('report_template_id')->nullable();

            // --- Status & Audit Trail ---
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->comment('ID ผู้ใช้งานที่สร้าง');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('ID ผู้ใช้งานที่แก้ไขล่าสุด');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('ID ผู้ใช้งานที่ลบ');
            
            $table->timestamps();
            $table->softDeletes(); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('form_groups');
    }
};