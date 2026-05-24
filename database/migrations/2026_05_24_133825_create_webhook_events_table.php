<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('webhook_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('source', ['stripe', 'gemini', 'lyria', 'internal']);
            $table->string('event_type');
            $table->string('idempotency_key')->unique();
            $table->json('payload');
            $table->boolean('signature_verified')->default(false);
            $table->enum('status', ['received', 'processing', 'processed', 'failed'])->default('received');
            $table->text('error_message')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('created_at');
        });
    }

    public function down(): void {
        Schema::dropIfExists('webhook_events');
    }
};
