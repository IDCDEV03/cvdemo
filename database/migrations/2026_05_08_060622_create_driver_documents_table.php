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
        Schema::create('drivers_document', function (Blueprint $table) {
             $table->id();
            $table->string('driver_id', 30)->comment('FK to drivers_detail.driver_id');
            $table->string('doc_type', 50)->comment('license=ใบขับขี่, id_card=บัตรปชช, all=รวมชุดเอกสาร');
            $table->string('doc_name', 200)->comment('Display name');
            $table->string('file_path', 255)->comment('Relative path');
            $table->string('file_original_name', 255)->nullable();
            $table->string('file_extension', 10)->nullable()->comment('pdf, jpg, png');
            $table->integer('file_size')->nullable()->comment('Bytes');
            $table->string('uploaded_by', 20)->comment('user_id of uploader');
            $table->tinyInteger('is_active')->default(1)->comment('1=current, 0=archived');
            $table->text('remark')->nullable();
            $table->timestamps();

            $table->index('driver_id');
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
        Schema::dropIfExists('driver_documents');
    }
};
