<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drivers_training', function (Blueprint $table) {
            $table->id();
            $table->string('driver_id', 30);
            $table->string('training_company', 50);
            $table->date('training_date');
            $table->date('training_expire_date');
            $table->string('created_by', 50)->nullable();
            $table->timestamp('created_at')->nullable();

            $table->foreign('driver_id')
                  ->references('driver_id')
                  ->on('drivers_detail')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drivers_training');
    }
};