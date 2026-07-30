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
        Schema::table('company_details', function (Blueprint $table) {            
            $table->integer('form_limit')->nullable()->after('company_name');
            $table->boolean('require_user_approval')
                  ->default(0)
                  ->after('form_limit');
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
        Schema::table('company_details', function (Blueprint $table) {
             $table->dropColumn([              
                'form_limit',
                'require_user_approval',
                'start_date',
                'expire_date'
            ]);
        });
    }
};
