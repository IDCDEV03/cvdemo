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
        Schema::create('vehicles_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('company_code',20);
            $table->string('car_id',20)->unique();
            $table->string('car_plate',100);
            $table->string('car_brand',50);
            $table->string('car_model',50); 
            $table->string('car_number_record',50);
            $table->string('car_age',20);    
            $table->string('car_tax',100);  
            $table->string('car_mileage',20);    
            $table->string('car_insure',100);  
            $table->string('car_type',20);   
            $table->enum('status', ['0', '1', '2']); // 1=ใช้งานได้, 2=รอซ่อม, 0=งดใช้งาน
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
        Schema::dropIfExists('vehicles_detail');
    }
};
