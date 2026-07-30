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
        Schema::create('pre_inspection_results', function (Blueprint $table) {
            $table->id();
            $table->string('record_id', 30)->comment('รหัสการตรวจ');
            $table->unsignedBigInteger('field_id')->comment('รหัสหัวข้อจาก pre_inspection_fields');
            $table->text('field_value')->nullable()->comment('ค่าที่กรอก (ข้อความ หรือ พิกัด GPS)');
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
        Schema::dropIfExists('pre_inspection_results');
    }
};
