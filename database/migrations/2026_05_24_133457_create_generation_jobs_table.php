<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('generation_jobs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('clip_id')->nullable();
            $table->string('job_class');
            $table->string('ai_provider')->nullable();
            $table->enum('status', ['pending', 'running', 'done', 'failed', 'cancelled'])->default('pending');
            $table->tinyInteger('progress_pct')->default(0);
            $table->string('progress_message')->nullable();
            $table->integer('credits_reserved')->default(0);
            $table->integer('credits_charged')->nullable();
            $table->string('provider_job_id')->nullable();
            $table->json('provider_response')->nullable();
            $table->text('error_message')->nullable();
            $table->tinyInteger('attempts')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('generation_jobs');
    }
};
