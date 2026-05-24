<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('clips', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('title');
            $table->text('lyrics_input')->nullable();
            $table->string('language', 10)->default('en');
            $table->enum('script_direction', ['ltr', 'rtl'])->default('ltr');
            $table->enum('status', ['draft', 'processing', 'ready', 'failed'])->default('draft');
            $table->enum('visibility', ['private', 'public'])->default('private');
            $table->json('ai_metadata')->nullable();
            $table->string('cover_image_key')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('clips');
    }
};
