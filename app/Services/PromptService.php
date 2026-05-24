<?php
namespace App\Services;
class PromptService
{
    private PronunciationService $pronunciation;
    public function __construct(PronunciationService $pronunciation)
    {
        $this->pronunciation = $pronunciation;
    }
    public function buildSongPrompt(array $params): string
    {
        $lyrics   = $params["lyrics"] ?? "";
        $language = $params["language"] ?? "en";
        $title    = $params["title"] ?? "";
        $hints = $this->pronunciation->injectHints($lyrics, $language);
        $prompt  = "Generate a complete song of exactly 59 to 60 seconds duration.\n";
        $prompt .= "Language: {$language}\n";
        if ($title) {
            $prompt .= "Title: {$title}\n";
        }
        $prompt .= "Lyrics:\n{$lyrics}\n";
        if ($hints) {
            $prompt .= "Pronunciation notes: {$hints}\n";
        }
        $prompt .= "The song must be between 59 and 60 seconds long. Do not exceed 60 seconds.\n";
        return trim($prompt);
    }
    public function buildBedPrompt(array $params): string
    {
        $lyrics   = $params["lyrics"] ?? "";
        $language = $params["language"] ?? "en";
        $prompt  = "Generate instrumental background music of up to 3 minutes (180 seconds).\n";
        $prompt .= "Language mood: {$language}\n";
        $prompt .= "Inspired by these lyrics (instrumental only, no vocals):\n{$lyrics}\n";
        $prompt .= "Duration: up to 180 seconds. No vocals.\n";
        return trim($prompt);
    }
    public function buildCoverPrompt(array $params): string
    {
        $lyrics      = $params["lyrics"] ?? "";
        $title       = $params["title"] ?? "";
        $description = $params["description"] ?? "";
        $prompt  = "Generate a music album cover image.\n";
        if ($title) {
            $prompt .= "Song title: {$title}\n";
        }
        if ($description) {
            $prompt .= "Description: {$description}\n";
        } else {
            $prompt .= "Inspired by these lyrics: {$lyrics}\n";
        }
        $prompt .= "Style: artistic, modern, suitable for a music streaming platform.\n";
        return trim($prompt);
    }
}
