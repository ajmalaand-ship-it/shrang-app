<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('reels', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('clip_id');
            $table->uuid('media_asset_id')->nullable();
            $table->enum('status', ['pending', 'processing', 'ready', 'failed'])->default('pending');
            $table->foreign('clip_id')->references('id')->on('clips')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('reels');
    }
};
