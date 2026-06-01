<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PashtoLanguageHintsSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = '019e5ce9-b213-7176-b5b9-0598fa6c4927';
        $now = now();

        $hints = [
            [
                'word'             => 'ښ',
                'phoneme_hint'     => 'retroflex sh, tongue tip curled back to palate, deeper than English sh',
                'prompt_injection' => 'The Pashto letter ښ is a retroflex sh sound. Pronounce it like sh in shoe but with the tongue tip curled back toward the palate. This sound is unique to Pashto.',
                'notes'            => 'Retroflex sh — unique to Pashto, universally agreed across dialects',
            ],
            [
                'word'             => 'ږ',
                'phoneme_hint'     => 'zh sound, like s in measure or French j in je',
                'prompt_injection' => 'The Pashto letter ږ is a zh sound, like the s in the English word measure or the French j in je.',
                'notes'            => 'Pashto zh — universally agreed, often mispronounced as z or j',
            ],
            [
                'word'             => 'ټ',
                'phoneme_hint'     => 'retroflex t, tongue tip curled back to palate',
                'prompt_injection' => 'The Pashto letter ټ is a retroflex t. Pronounce it with the tongue tip curled back to touch the palate, giving a deeper t sound than English t.',
                'notes'            => 'Retroflex t — universally agreed across dialects',
            ],
            [
                'word'             => 'ډ',
                'phoneme_hint'     => 'retroflex d, tongue tip curled back to palate',
                'prompt_injection' => 'The Pashto letter ډ is a retroflex d. Pronounce it with the tongue tip curled back, giving a deeper d sound than English d.',
                'notes'            => 'Retroflex d — universally agreed across dialects',
            ],
            [
                'word'             => 'ڼ',
                'phoneme_hint'     => 'retroflex nasal n, tongue tip curled back',
                'prompt_injection' => 'The Pashto letter ڼ is a retroflex nasal n, pronounced with the tongue tip curled back toward the palate.',
                'notes'            => 'Retroflex n — universally agreed across dialects',
            ],
            [
                'word'             => 'مينه',
                'phoneme_hint'     => 'meena — love, long ee vowel, mee-na',
                'prompt_injection' => 'مينه means love in Pashto. Pronounced meena with a long ee vowel. This is the most common word in Pashto romantic poetry.',
                'notes'            => 'Love — universal across all Pashto dialects',
            ],
            [
                'word'             => 'زړه',
                'phoneme_hint'     => 'zrra — heart, z then retroflex r then a',
                'prompt_injection' => 'زړه means heart in Pashto. Pronounced zrra with a retroflex r quality. Extremely common in Pashto poetry.',
                'notes'            => 'Heart — universal, most common poetic word in Pashto',
            ],
            [
                'word'             => 'لمر',
                'phoneme_hint'     => 'lmar — sun, single syllable, l then mar',
                'prompt_injection' => 'لمر means sun in Pashto. Pronounced as one syllable lmar with a clear l followed by mar.',
                'notes'            => 'Sun — universal nature word in Pashto poetry',
            ],
            [
                'word'             => 'سپوږمۍ',
                'phoneme_hint'     => 'spogmai — moon, spo then gmai',
                'prompt_injection' => 'سپوږمۍ means moon in Pashto. Pronounced spogmai where the ږ gives a zh-g quality. Also a common Pashto female name.',
                'notes'            => 'Moon — universal, common in romantic poetry and as a name',
            ],
            [
                'word'             => 'غزل',
                'phoneme_hint'     => 'ghazal — lyric poem form, gh is voiced guttural from back of throat',
                'prompt_injection' => 'غزل is the ghazal, a classical poetic and musical form. Pronounced gha-zel where gh is a voiced guttural sound made in the back of the throat, similar to a gargling r.',
                'notes'            => 'Ghazal poetry form — not dialect-specific, musical term',
            ],
        ];

        foreach ($hints as $hint) {
            DB::table('language_hints')->updateOrInsert(
                [
                    'language_code' => 'ps',
                    'word'          => $hint['word'],
                ],
                [
                    'phoneme_hint'     => $hint['phoneme_hint'],
                    'prompt_injection' => $hint['prompt_injection'],
                    'provider'        => null,
                    'is_active'       => true,
                    'notes'           => $hint['notes'],
                    'created_by'      => $adminId,
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ]
            );
        }

        $this->command->info('Seeded ' . count($hints) . ' Pashto language hints (idempotent).');
    }
}
