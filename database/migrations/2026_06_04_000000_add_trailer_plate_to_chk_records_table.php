<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chk_records', function (Blueprint $table) {
            // Trailer plate at time of inspection (may differ from vehicles_detail.car_trailer_plate)
            $table->string('trailer_plate', 50)->nullable()->after('veh_id');
        });
    }

    public function down(): void
    {
        Schema::table('chk_records', function (Blueprint $table) {
            $table->dropColumn('trailer_plate');
        });
    }
};
