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
        // Skip columns that were already added outside of migrations
        Schema::table('vehicle_supply_history', function (Blueprint $table) {
            if (!Schema::hasColumn('vehicle_supply_history', 'car_id'))
                $table->string('car_id', 30)->after('id')->comment('รหัสรถ');
            if (!Schema::hasColumn('vehicle_supply_history', 'from_supply_id'))
                $table->string('from_supply_id', 20)->nullable()->after('car_id')->comment('supply เดิม');
            if (!Schema::hasColumn('vehicle_supply_history', 'from_supply_name'))
                $table->string('from_supply_name', 255)->nullable()->after('from_supply_id');
            if (!Schema::hasColumn('vehicle_supply_history', 'to_supply_id'))
                $table->string('to_supply_id', 20)->nullable()->after('from_supply_name')->comment('supply ใหม่');
            if (!Schema::hasColumn('vehicle_supply_history', 'to_supply_name'))
                $table->string('to_supply_name', 255)->nullable()->after('to_supply_id');
            if (!Schema::hasColumn('vehicle_supply_history', 'changed_by_user_id'))
                $table->string('changed_by_user_id', 30)->nullable()->after('to_supply_name');
            if (!Schema::hasColumn('vehicle_supply_history', 'changed_by_name'))
                $table->string('changed_by_name', 255)->nullable()->after('changed_by_user_id');
            if (!Schema::hasColumn('vehicle_supply_history', 'changed_at'))
                $table->timestamp('changed_at')->nullable()->after('changed_by_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vehicle_supply_history', function (Blueprint $table) {
            $table->dropColumn([
                'car_id', 'from_supply_id', 'from_supply_name',
                'to_supply_id', 'to_supply_name',
                'changed_by_user_id', 'changed_by_name', 'changed_at',
            ]);
        });
    }
};
