<?php
namespace App\Services;
class PromptService
{
    private PronunciationService $pronunciation;
    public function __construct(PronunciationService $pronunciation)
    {
        $this->pronunciation = $pronunciation;
    }
    private array $languageLabels = [
        'ps' => 'Pashto',
        'fa' => 'Dari/Farsi',
        'ur' => 'Urdu',
        'ar' => 'Arabic',
        'hi' => 'Hindi',
        'en' => 'English',
    ];
    private array $stylesByLanguage = [
        'ps' => [
            'pashto_folk'     => 'Pashto folk',
            'attan_wedding'   => 'Attan / wedding',
            'rubab_tabla'     => 'Rubab and tabla',
            'slow_ghazal'     => 'Slow Pashto ghazal',
            'sad_migration'   => 'Sad migration song',
            'romantic'        => 'Romantic',
            'patriotic'       => 'Patriotic',
            'modern_emotional'=> 'Modern emotional',
        ],
        'fa' => [
            'classical_persian' => 'Classical Persian',
            'afghan_folk'       => 'Afghan folk',
            'ghazal'            => 'Ghazal',
            'pop'               => 'Modern pop',
            'romantic'          => 'Romantic',
            'sad'               => 'Sad and reflective',
        ],
        'ur' => [
            'ghazal'     => 'Ghazal',
            'qawwali'    => 'Qawwali',
            'classical'  => 'Classical',
            'romantic'   => 'Romantic',
            'sad'        => 'Sad',
            'pop'        => 'Modern pop',
        ],
        'ar' => [
            'arabic_classical' => 'Arabic classical',
            'khaleeji'         => 'Khaleeji',
            'romantic'         => 'Romantic',
            'sad'              => 'Sad',
            'pop'              => 'Modern pop',
        ],
        'hi' => [
            'bollywood'  => 'Bollywood',
            'classical'  => 'Hindustani classical',
            'folk'       => 'Indian folk',
            'romantic'   => 'Romantic',
            'sad'        => 'Sad and emotional',
            'devotional' => 'Devotional',
        ],
        'en' => [
            'pop'       => 'Pop',
            'folk'      => 'Folk',
            'rnb'       => 'R&B / Soul',
            'cinematic' => 'Cinematic',
            'acoustic'  => 'Acoustic',
            'sad'       => 'Sad and emotional',
        ],
    ];
    public function getStylesForLanguage(string $language): array
    {
        return $this->stylesByLanguage[$language] ?? $this->stylesByLanguage['en'];
    }
    public function buildSongPrompt(array $params): string
    {
        $lyrics    = $params['lyrics'] ?? '';
        $language  = $params['language'] ?? 'en';
        $title     = $params['title'] ?? '';
        $style     = $params['style'] ?? '';
        $voice     = $params['voice'] ?? '';
        $direction = $params['creative_direction'] ?? '';
        $langLabel = $this->languageLabels[$language] ?? $language;
        $hints = $this->pronunciation->injectHints($lyrics, $language);
        $prompt  = "Create an original song of exactly 59 to 60 seconds.\n";
        $prompt .= "Language: {$langLabel}\n";
        if ($style) $prompt .= "Music style: {$style}\n";
        if ($voice && $voice !== 'no_preference') {
            $voiceLabel = $voice === 'male' ? 'male vocal, male singer, male voice' : 'female vocal, female singer, female voice';
            $prompt .= "Voice: {$voiceLabel}\n";
            $prompt .= "Important: use a {$voice} singer only.\n";
        }
        if ($title) $prompt .= "Title: {$title}\n";
        if ($direction) $prompt .= "Creative direction: {$direction}\n";
        $prompt .= "Lyrics:\n{$lyrics}\n";
        if ($hints) $prompt .= "Pronunciation notes: {$hints}\n";
        $prompt .= "Sing the lyrics exactly as written. Do not translate or rewrite them. Do not imitate any existing artist or song.\n";
        $prompt .= "Duration: exactly 59-60 seconds.\n";
        return trim($prompt);
    }
    public function buildBedPrompt(array $params): string
    {
        $description = $params['lyrics'] ?? '';
        $language    = $params['language'] ?? 'en';
        $mood        = $params['mood'] ?? '';
        $purpose     = $params['purpose'] ?? '';
        $langLabel   = $this->languageLabels[$language] ?? $language;
        $prompt  = "Create original instrumental background music. No vocals. No lyrics.\n";
        $prompt .= "Duration: up to 3 minutes (180 seconds).\n";
        if ($mood)    $prompt .= "Mood: {$mood}\n";
        if ($purpose) $prompt .= "Purpose: {$purpose}\n";
        $prompt .= "Cultural/language inspiration: {$langLabel}\n";
        if ($description) $prompt .= "Creative direction: {$description}\n";
        $prompt .= "Keep the music smooth, clean, and suitable for use under narration or video. Do not imitate any existing work.\n";
        return trim($prompt);
    }
    public function buildCoverPrompt(array $params): string
    {
        $lyrics      = $params['lyrics'] ?? '';
        $title       = $params['title'] ?? '';
        $description = $params['description'] ?? '';
        $prompt  = "Generate a music album cover image.\n";
        if ($title)       $prompt .= "Song title: {$title}\n";
        if ($description) $prompt .= "Description: {$description}\n";
        else              $prompt .= "Inspired by these lyrics: {$lyrics}\n";
        $prompt .= "Style: artistic, modern, suitable for a music streaming platform.\n";
        return trim($prompt);
    }
}
