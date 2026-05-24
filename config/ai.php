<?php
return [
    "default_music_provider" => env("AI_MUSIC_PROVIDER", "lyria"),
    "default_lyrics_provider" => env("AI_LYRICS_PROVIDER", "gemini"),
    "default_cover_provider" => env("AI_COVER_PROVIDER", "stability"),
    "lyria" => [
        "api_key"  => env("LYRIA_API_KEY", ""),
        "base_url" => env("LYRIA_BASE_URL", "https://aiplatform.googleapis.com"),
        "song_duration_seconds" => 60,
        "bed_duration_seconds"  => 180,
    ],
    "gemini" => [
        "api_key"  => env("GEMINI_API_KEY", ""),
        "base_url" => env("GEMINI_BASE_URL", "https://generativelanguage.googleapis.com"),
    ],
    "elevenlabs" => [
        "api_key"  => env("ELEVENLABS_API_KEY", ""),
        "base_url" => env("ELEVENLABS_BASE_URL", "https://api.elevenlabs.io"),
    ],
    "stability" => [
        "api_key"  => env("STABILITY_API_KEY", ""),
        "base_url" => env("STABILITY_BASE_URL", "https://api.stability.ai"),
    ],
    "fake" => [
        "enabled" => env("AI_FAKE_PROVIDER", false),
    ],
];
