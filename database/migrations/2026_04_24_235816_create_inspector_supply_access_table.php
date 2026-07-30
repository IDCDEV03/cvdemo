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
        Schema::create('inspector_supply_access', function (Blueprint $table) {
            $table->id();           
       
            $table->string('ins_id', 30)->comment('รหัสพนักงานตรวจ');
            $table->string('supply_id', 30)->comment('รหัส Supply ที่อนุญาตให้เข้าถึง');
            $table->string('assigned_by',30)->nullable()->comment('ID ของ Staff ที่เป็นคนกำหนดสิทธิ์');
            
            $table->timestamps();
            $table->unique(['ins_id', 'supply_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inspector_supply_access');
    }
};
