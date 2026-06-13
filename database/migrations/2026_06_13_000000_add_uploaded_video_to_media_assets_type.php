<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement(
            "ALTER TABLE media_assets MODIFY COLUMN type " .
            "ENUM('song_audio','bed_audio','uploaded_audio','cover_image','reel_video','uploaded_video') NOT NULL"
        );
    }

    public function down(): void
    {
        DB::statement(
            "ALTER TABLE media_assets MODIFY COLUMN type " .
            "ENUM('song_audio','bed_audio','uploaded_audio','cover_image','reel_video') NOT NULL"
        );
    }
};
