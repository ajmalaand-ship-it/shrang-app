<?php

namespace App\Http\Controllers\Studio;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateBedMusicJob;
use App\Jobs\GenerateSongJob;
use App\Models\Clip;
use App\Models\GenerationJob;
use App\Services\GenerationBillingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AudioController extends Controller
{
    public function __construct(
        private readonly GenerationBillingService $billing,
    ) {}

    public function regenerate(Request $request, Clip $clip): RedirectResponse
    {
        $this->authorize('update', $clip);

        $validated = $request->validate([
            'lyrics'             => ['nullable', 'string', 'max:5000'],
            'style'              => ['nullable', 'string', 'max:100'],
            'voice'              => ['nullable', 'in:male,female,no_preference'],
            'creative_direction' => ['nullable', 'string', 'max:500'],
        ]);

        $runningAudioJob = $clip->generationJobs()
            ->where(function ($query) {
                $query->where('job_class', 'LIKE', '%GenerateSongJob%')
                    ->orWhere('job_class', 'LIKE', '%GenerateBedMusicJob%');
            })
            ->whereIn('status', ['pending', 'running'])
            ->latest()
            ->first();

        if ($runningAudioJob) {
            return redirect()
                ->route('studio.show', $clip)
                ->with('info', 'Audio regeneration is already running.');
        }

        $audioAsset = $clip->mediaAssets()
            ->whereIn('type', ['song_audio', 'bed_audio', 'uploaded_audio'])
            ->where('is_primary', true)
            ->first();

        if (!$audioAsset) {
            return redirect()
                ->route('studio.show', $clip)
                ->with('error', 'No audio was found for this clip.');
        }

        if ($audioAsset->type === 'uploaded_audio') {
            return redirect()
                ->route('studio.show', $clip)
                ->with('error', 'Uploaded audio cannot be regenerated. You can upload a new audio file from Create.');
        }

        $user = $request->user();
        $lyricsForRegeneration = $validated['lyrics'] ?? $clip->lyrics_input ?? '';

        $isBedMusic = $audioAsset->type === 'bed_audio';
        $jobClass = $isBedMusic ? GenerateBedMusicJob::class : GenerateSongJob::class;
        $billingType = $isBedMusic ? 'bed' : 'song';

        $genJob = GenerationJob::create([
            'user_id'          => $user->id,
            'clip_id'          => $clip->id,
            'job_class'        => $jobClass,
            'ai_provider'      => 'lyria',
            'status'           => 'pending',
            'progress_pct'     => 0,
            'credits_reserved' => 0,
        ]);

        $billing = $this->billing->checkAndReserve($user, $billingType, $genJob->id);

        if (!$billing['ok']) {
            $genJob->delete();

            return redirect()
                ->route('studio.show', $clip)
                ->withErrors(['credits' => $billing['message']]);
        }

        // Keep the current audio playable while regeneration runs.

        if ($isBedMusic) {
            GenerateBedMusicJob::dispatch($genJob->id, [
                'user_id'           => $user->id,
                'generation_job_id' => $genJob->id,
                'lyrics'            => $lyricsForRegeneration,
                'language'          => $clip->language,
                'mood'              => $validated['style'] ?? '',
                'purpose'           => $validated['creative_direction'] ?? '',
                'title'             => $clip->title ?: 'Background Music',
                'is_regeneration'   => true,
            ])->onQueue('ai-generation');
        } else {
            GenerateSongJob::dispatch($genJob->id, [
                'user_id'            => $user->id,
                'generation_job_id'  => $genJob->id,
                'lyrics'             => $lyricsForRegeneration,
                'language'           => $clip->language,
                'title'              => $clip->title ?? '',
                'style'              => $validated['style'] ?? '',
                'voice'              => $validated['voice'] ?? 'no_preference',
                'creative_direction' => $validated['creative_direction'] ?? '',
                'is_regeneration'    => true,
            ])->onQueue('ai-generation');
        }

        return redirect()
            ->route('studio.show', $clip)
            ->with('success', 'Audio regeneration started.');
    }
}
