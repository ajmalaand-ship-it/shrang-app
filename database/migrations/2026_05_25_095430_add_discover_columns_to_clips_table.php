<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        Schema::table('clips', function (Blueprint $table) {
            $table->boolean('allow_download')->default(true)->after('visibility');
            $table->boolean('lyrics_public')->default(false)->after('allow_download');
        });
    }
    public function down(): void
    {
        Schema::table('clips', function (Blueprint $table) {
            $table->dropColumn(['allow_download', 'lyrics_public']);
        });
    }
};
