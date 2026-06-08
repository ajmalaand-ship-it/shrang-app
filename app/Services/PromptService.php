<?php
namespace App\Services;
class PromptService
{
    private PronunciationService $pronunciation;
    private AdminSettingsService $settings;
    public function __construct(PronunciationService $pronunciation, AdminSettingsService $settings)
    {
        $this->pronunciation = $pronunciation;
        $this->settings = $settings;
    }

    /**
     * Resolves the admin-selected song mode to a target duration in seconds.
     */
    private function songDurationSeconds(): int
    {
        return match ($this->settings->get("lyria_song_mode", "dev_pro_60")) {
            "dev_clip_30", "vertex_002_30" => 30,
            "dev_pro_180", "vertex_lyria3_pro" => 180,
            default => 60,
        };
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
        // Strip invisible Unicode control/format characters from prompt only
        // Original lyrics in DB and display are never modified
        $lyrics    = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}\x{00AD}]/u', '', $lyrics);
        $language  = $params['language'] ?? 'en';
        $title     = $params['title'] ?? '';
        $style     = $params['style'] ?? '';
        $voice     = $params['voice'] ?? '';
        $direction = $params['creative_direction'] ?? '';
        $langLabel = $this->languageLabels[$language] ?? $language;
        $hints = $this->pronunciation->injectHints($lyrics, $language);
        $seconds = $this->songDurationSeconds();
        $lengthPhrase = $seconds >= 180 ? "about 3 minutes (around 180 seconds), a full-length song with intro, verses, chorus, and a bridge" : ($seconds >= 60 ? "about {$seconds} seconds" : "about 30 seconds");
        $prompt  = "Create an original song of {$lengthPhrase}.\n";
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
        $prompt .= "Target duration: {$lengthPhrase}.\n";
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
        $title       = trim($params['title'] ?? '');
        $lyrics      = trim($params['lyrics'] ?? '');
        $language    = $params['language'] ?? 'en';
        $style       = $params['style'] ?? 'artistic';
        $mood        = $params['mood'] ?? '';
        $visualDir   = trim($params['visual_direction'] ?? '');
        $textOnCover = $params['text_on_cover'] ?? 'none';

        $langNames = [
            'ps' => 'Pashto', 'fa' => 'Dari/Farsi', 'ur' => 'Urdu',
            'ar' => 'Arabic', 'hi' => 'Hindi', 'en' => 'English',
        ];
        $langLabel = $langNames[$language] ?? 'English';

        $styleMap = [
            'artistic'  => 'artistic music album cover, painterly, expressive',
            'photo'     => 'photo-realistic scene, cinematic photography',
            'poetic'    => 'poetic and symbolic, metaphorical imagery',
            'cultural'  => 'traditional cultural style, respectful and authentic',
            'cinematic' => 'modern cinematic, dramatic lighting, film poster style',
            'minimal'   => 'minimal and clean, simple composition, elegant',
            'dramatic'  => 'dramatic emotional, intense, powerful imagery',
        ];
        $styleDesc = $styleMap[$style] ?? $styleMap['artistic'];

        $lyricsExcerpt = '';
        if ($lyrics) {
            $lines = array_filter(array_map('trim', explode("\n", $lyrics)));
            $lyricsExcerpt = implode(' / ', array_slice($lines, 0, 3));
        }

        $prompt  = "Create a professional music album cover image.\n";
        $prompt .= "Style: {$styleDesc}.\n";
        if ($mood)        $prompt .= "Mood: {$mood}.\n";
        if ($title)       $prompt .= "This is for a {$langLabel} song titled: {$title}.\n";
        if ($lyricsExcerpt) $prompt .= "Lyric themes: {$lyricsExcerpt}.\n";
        if ($visualDir)   $prompt .= "Visual direction: {$visualDir}.\n";

        if (in_array($language, ['ps', 'fa', 'ur', 'ar'])) {
            $prompt .= "Cultural context: This is for a {$langLabel} music artist. ";
            $prompt .= "The imagery must be respectful, modern, and culturally appropriate. ";
            $prompt .= "Avoid stereotypes. Use poetic and artistic visual metaphors.\n";
        }
        if ($textOnCover === 'none') {
            $prompt .= "Do not include any text, words, or lettering in the image.\n";
        } elseif ($textOnCover === 'title' && $title) {
            $prompt .= "Include the song title as elegant typography: {$title}.\n";
        }
        $prompt .= "Square format. High quality, professional music album artwork.\n";
        return trim($prompt);
    }
}
