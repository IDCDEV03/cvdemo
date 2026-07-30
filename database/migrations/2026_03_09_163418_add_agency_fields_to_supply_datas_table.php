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
        Schema::table('supply_datas', function (Blueprint $table) {
            $table->string('agency_user_id',20)->nullable()->after('company_code');
            $table->integer('vehicle_limit')->nullable()->after('supply_status');
            $table->boolean('require_user_approval')
                  ->default(0)
                  ->after('vehicle_limit');
            $table->date('start_date')->nullable()->after('require_user_approval');
            $table->date('expire_date')->nullable()->after('start_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('supply_datas', function (Blueprint $table) {
               $table->dropColumn([
                'agency_user_id',
                'vehicle_limit',
                'require_user_approval',
                'start_date',
                'expire_date'
            ]);
        });
    }
};
