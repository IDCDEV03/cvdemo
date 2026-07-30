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
        Schema::table('chk_records', function (Blueprint $table) {
             $table->dropColumn(['img_front', 'img_beside', 'img_overall']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chk_records', function (Blueprint $table) {
            $table->string('img_front')->nullable()->after('agency_id'); 
            $table->string('img_beside')->nullable()->after('img_front'); 
            $table->string('img_overall')->nullable()->after('img_beside'); 
        });
    }
};
