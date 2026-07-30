<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('driver_audit_logs')) {
            return;
        }

        Schema::create('driver_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('driver_id', 30)->index();

            // Action types: create_driver | update_driver | upload_document | delete_document
            $table->string('action', 50);

            $table->string('performed_by', 30);
            $table->string('performed_by_role', 20)->nullable();

            // JSON diff of changed fields: {"field": {"old": "x", "new": "y"}}
            $table->json('field_changes')->nullable();

            // Extra context: doc_id, doc_type, doc_name, file_path, etc.
            $table->json('meta')->nullable();

            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_audit_logs');
    }
};
