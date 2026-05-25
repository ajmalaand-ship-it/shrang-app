<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        Schema::create('clip_features', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('clip_id')->unique();
            $table->uuid('featured_by');
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_blocked')->default(false);
            $table->integer('sort_order')->nullable();
            $table->timestamp('featured_at')->useCurrent();
            $table->timestamp('created_at')->useCurrent();
            $table->foreign('clip_id')->references('id')->on('clips')->onDelete('cascade');
            $table->foreign('featured_by')->references('id')->on('users')->onDelete('cascade');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('clip_features');
    }
};
