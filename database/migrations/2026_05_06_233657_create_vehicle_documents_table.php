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
       Schema::create('vehicle_documents', function (Blueprint $table) {
            $table->id();
            $table->string('veh_id', 30)->comment('FK to vehicles_detail.car_id');
            $table->string('doc_name', 200)->comment('Display name');
            $table->string('file_path', 255)->comment('Relative path');
            $table->string('file_original_name', 255)->nullable();
            $table->string('file_extension', 10)->nullable()->comment('pdf or docx');
            $table->integer('file_size')->nullable()->comment('Bytes');
            $table->string('uploaded_by', 20)->comment('user_id of uploader');
            $table->boolean('is_active')->default(1)->comment('1=current, 0=archived');
            $table->text('remark')->nullable();
            $table->timestamps();

            $table->index('veh_id');
            $table->index('is_active');
            $table->index('uploaded_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicle_documents');
    }
};
