<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        Schema::create('clip_stats', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('clip_id')->unique();
            $table->unsignedInteger('play_count')->default(0);
            $table->unsignedInteger('download_count')->default(0);
            $table->unsignedInteger('like_count')->default(0);
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->foreign('clip_id')->references('id')->on('clips')->onDelete('cascade');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('clip_stats');
    }
};
