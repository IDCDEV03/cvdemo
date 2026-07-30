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
        Schema::table('users', function (Blueprint $table) {
            $table->string('agency_user_id', 20)->nullable()->after('company_code');
            $table->string('supply_user_id', 20)->nullable()->after('agency_user_id');
            $table->boolean('is_approved')
                ->default(0)
                ->after('user_phone');
            $table->string('approved_by', 20)->nullable()->after('is_approved');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
              $table->dropColumn([
                'agency_user_id',
                'supply_user_id',
                'is_approved',
                'approved_by'
            ]);
        });
    }
};
