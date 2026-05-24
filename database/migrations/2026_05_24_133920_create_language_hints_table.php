<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('language_hints', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('language_code', 10);
            $table->string('word');
            $table->string('phoneme_hint')->nullable();
            $table->text('prompt_injection')->nullable();
            $table->string('provider')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->uuid('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('language_hints');
    }
};
