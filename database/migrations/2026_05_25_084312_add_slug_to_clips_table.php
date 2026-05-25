<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        Schema::table("clips", function (Blueprint $table) {
            $table->string("slug")->nullable()->after("title");
        });

        // Populate slugs for existing clips
        $clips = DB::table("clips")->orderBy("created_at")->get(["id", "title"]);
        foreach ($clips as $clip) {
            $base = Str::slug($clip->title) ?: "clip";
            $slug = $base;
            $i = 2;
            while (DB::table("clips")->where("slug", $slug)->where("id", "!=", $clip->id)->exists()) {
                $slug = $base . "-" . $i++;
            }
            DB::table("clips")->where("id", $clip->id)->update(["slug" => $slug]);
        }

        Schema::table("clips", function (Blueprint $table) {
            $table->string("slug")->unique()->change();
        });
    }

    public function down(): void
    {
        Schema::table("clips", function (Blueprint $table) {
            $table->dropColumn("slug");
        });
    }
};
