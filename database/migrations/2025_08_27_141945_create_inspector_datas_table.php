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
        Schema::create('inspector_datas', function (Blueprint $table) {
          $table->id();
            $table->string('ins_id',20)->unique();
            $table->string('sup_id',20);
            $table->string('ins_prefix',20);
            $table->string('ins_name',50);
            $table->string('ins_lastname',50);
            $table->string('dl_number',50)->unique();
            $table->string('ins_phone',50)->nullable();
            $table->string('ins_birthyear',50)->nullable();
            $table->string('ins_experience',50)->nullable();
            $table->enum('ins_status', ['0', '1'])->default('1'); // 1=ใช้งานได้, 0=ปิดการใช้
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
        Schema::dropIfExists('inspector_datas');
    }
};
