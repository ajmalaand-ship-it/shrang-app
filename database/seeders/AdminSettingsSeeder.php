<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class AdminSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ["key" => "credit_cost_song",        "value" => "10",  "cast" => "int",  "group" => "credits", "label" => "Credits per song generation",       "is_public" => false],
            ["key" => "credit_cost_bed",         "value" => "5",   "cast" => "int",  "group" => "credits", "label" => "Credits per bed music generation",   "is_public" => false],
            ["key" => "credit_cost_upload",      "value" => "2",   "cast" => "int",  "group" => "credits", "label" => "Credits per audio upload",           "is_public" => false],
            ["key" => "credit_cost_cover",       "value" => "3",   "cast" => "int",  "group" => "credits", "label" => "Credits per cover generation",       "is_public" => false],
            ["key" => "credit_cost_reel",        "value" => "5",   "cast" => "int",  "group" => "credits", "label" => "Credits per reel generation",        "is_public" => false],
            ["key" => "free_tier_daily_song",    "value" => "2",   "cast" => "int",  "group" => "limits",  "label" => "Free tier daily song limit",         "is_public" => true],
            ["key" => "free_tier_daily_bed",     "value" => "1",   "cast" => "int",  "group" => "limits",  "label" => "Free tier daily bed music limit",    "is_public" => true],
            ["key" => "free_tier_bonus",         "value" => "20",  "cast" => "int",  "group" => "credits", "label" => "Registration bonus credits",         "is_public" => true],
            ["key" => "song_duration_seconds",   "value" => "60",  "cast" => "int",  "group" => "ai",      "label" => "Song clip duration (seconds)",       "is_public" => false],
            ["key" => "bed_duration_seconds",    "value" => "180", "cast" => "int",  "group" => "ai",      "label" => "Bed music duration (seconds)",       "is_public" => false],
            ["key" => "ai_music_provider",       "value" => "lyria","cast" => "string","group" => "ai",    "label" => "Music generation provider",          "is_public" => false],
            ["key" => "maintenance_mode",        "value" => "0",   "cast" => "bool", "group" => "features","label" => "Maintenance mode",                   "is_public" => true],
        ];
        foreach ($settings as $setting) {
            DB::table("admin_settings")->updateOrInsert(
                ["key" => $setting["key"]],
                array_merge($setting, ["updated_at" => now()])
            );
        }
    }
}
