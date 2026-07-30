<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles_detail', function (Blueprint $table) {
            $table->unsignedBigInteger('car_insurance_id')->nullable()->after('car_insure');
            $table->date('car_insurance_start')->nullable()->after('car_insurance_id');
        });
    }

    public function down(): void
    {
        Schema::table('vehicles_detail', function (Blueprint $table) {
            $table->dropColumn(['car_insurance_id', 'car_insurance_start']);
        });
    }
};