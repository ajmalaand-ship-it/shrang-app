<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        Schema::create('clip_likes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('clip_id');
            $table->uuid('user_id')->nullable();
            $table->string('guest_token')->nullable();
            $table->string('ip_address', 45);
            $table->timestamp('created_at')->useCurrent();
            $table->foreign('clip_id')->references('id')->on('clips')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['clip_id', 'user_id']);
            $table->unique(['clip_id', 'guest_token']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('clip_likes');
    }
};
