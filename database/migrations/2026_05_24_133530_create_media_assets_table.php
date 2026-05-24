<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('media_assets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('clip_id');
            $table->uuid('user_id');
            $table->uuid('generation_job_id')->nullable();
            $table->enum('type', ['song_audio', 'bed_audio', 'uploaded_audio', 'cover_image', 'reel_video']);
            $table->string('storage_disk')->default('s3');
            $table->string('storage_key');
            $table->string('cdn_url')->nullable();
            $table->string('mime_type')->nullable();
            $table->bigInteger('file_size_bytes')->default(0);
            $table->integer('duration_seconds')->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_temp')->default(false);
            $table->json('metadata')->nullable();
            $table->foreign('clip_id')->references('id')->on('clips')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('media_assets');
    }
};
