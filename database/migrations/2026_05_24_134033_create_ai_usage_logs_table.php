<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('ai_usage_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable();
            $table->uuid('generation_job_id')->nullable();
            $table->string('provider');
            $table->enum('capability', ['song', 'bed', 'voice', 'cover', 'transcribe']);
            $table->integer('prompt_tokens')->nullable();
            $table->integer('output_tokens')->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->decimal('provider_cost_usd', 10, 6)->nullable();
            $table->integer('latency_ms')->nullable();
            $table->enum('status', ['success', 'error', 'timeout'])->default('success');
            $table->string('error_code')->nullable();
            $table->timestamp('created_at');
        });
    }

    public function down(): void {
        Schema::dropIfExists('ai_usage_logs');
    }
};
