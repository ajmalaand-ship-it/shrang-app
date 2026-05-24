<?php
return [
    "costs" => [
        "song"   => (int) env("CREDIT_COST_SONG", 10),
        "bed"    => (int) env("CREDIT_COST_BED", 5),
        "upload" => (int) env("CREDIT_COST_UPLOAD", 2),
        "cover"  => (int) env("CREDIT_COST_COVER", 3),
        "reel"   => (int) env("CREDIT_COST_REEL", 5),
    ],
    "free_tier" => [
        "daily_song_limit"   => (int) env("FREE_TIER_DAILY_SONGS", 2),
        "daily_bed_limit"    => (int) env("FREE_TIER_DAILY_BED", 1),
        "registration_bonus" => (int) env("FREE_TIER_BONUS", 20),
    ],
    "reservation_ttl_minutes" => (int) env("CREDIT_RESERVATION_TTL", 10),
];
