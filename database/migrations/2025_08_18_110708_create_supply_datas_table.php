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
        Schema::create('supply_datas', function (Blueprint $table) {
            $table->id();
            $table->string('company_code',20);
            $table->string('sup_id',15)->unique();
            $table->string('supply_name',200);
            $table->tinyText('supply_address');
            $table->string('supply_phone',50)->nullable();
            $table->string('supply_email',50)->nullable();
            $table->enum('supply_status', ['0', '1'])->default('1'); // 1=ใช้งานได้, 0=ปิดการใช้
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
        Schema::dropIfExists('supply_datas');
    }
};
