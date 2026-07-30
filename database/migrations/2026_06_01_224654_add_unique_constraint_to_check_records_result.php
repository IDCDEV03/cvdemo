<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Remove duplicate rows before adding the constraint.
        // Keep the row with the highest id (most recent save) for each (record_id, item_id) pair.
        DB::statement('
            DELETE t1 FROM check_records_result t1
            INNER JOIN check_records_result t2
                ON  t1.record_id = t2.record_id
                AND t1.item_id   = t2.item_id
                AND t1.id < t2.id
        ');

        Schema::table('check_records_result', function (Blueprint $table) {
            $table->unique(['record_id', 'item_id'], 'chk_result_record_item_unique');
        });
    }

    public function down(): void
    {
        Schema::table('check_records_result', function (Blueprint $table) {
            $table->dropUnique('chk_result_record_item_unique');
        });
    }
};
