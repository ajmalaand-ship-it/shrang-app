@extends('layouts.app')

@section('title', $clip->title . ' — Clip Studio')

@if ($clip->status === 'processing')
@section('head_extra')
<meta http-equiv="refresh" content="5">
@endsection
@endif

@section('content')
<div class="sh-page-wrap">

    <div class="sh-section">
        <h1 class="sh-heading">{{ $clip->title }}</h1>
        <span class="sh-badge sh-badge--lang">{{ strtoupper($clip->language) }}</span>
    </div>

    @if (session('success'))
        <div class="sh-notice sh-notice--success">{{ session('success') }}</div>
    @endif

    {{-- PROCESSING STATE --}}
    @if ($clip->status === 'processing')
        <div class="sh-card">
            <div class="sh-card__body" style="text-align:center; padding: 3rem;">
                <p class="sh-heading" style="margin-bottom:1rem;">Generating your song...</p>
                <p class="sh-text-muted">This usually takes 30–60 seconds. This page refreshes automatically every 5 seconds.</p>
                <div style="margin-top:2rem;">
                    <div class="sh-phoneme-hint">Please wait while your song is being created.</div>
                </div>
            </div>
        </div>

    {{-- FAILED STATE --}}
    @elseif ($clip->status === 'failed')
        <div class="sh-card">
            <div class="sh-card__body">
                <div class="sh-notice sh-notice--danger">
                    Song generation failed. Your credits have been released.
                    @if ($latestJob && $latestJob->error_message)
                        <br><small>{{ $latestJob->error_message }}</small>
                    @endif
                </div>
                <a href="{{ route('create') }}" class="sh-btn sh-btn--primary" style="margin-top:1rem; display:inline-block;">
                    Try Again
                </a>
            </div>
        </div>

    {{-- READY STATE --}}
    @else
        <div class="sh-card">
            <div class="sh-card__header">Your Song</div>
            <div class="sh-card__body">

                {{-- Audio player --}}
                @if ($audioAsset)
                    <audio controls style="width:100%; margin-bottom:1rem;">
                        <source src="{{ $audioAsset->cdn_url }}" type="audio/mpeg">
                        Your browser does not support the audio element.
                    </audio>
                @else
                    <div class="sh-notice sh-notice--info">Audio is ready. Player coming soon.</div>
                @endif

                {{-- Lyrics --}}
                <div style="margin-top:1.5rem; padding-top:1.5rem; border-top:1px solid var(--sh-color-border, #e5e7eb);">
                    <label class="sh-label">Lyrics</label>
                    <p class="{{ $clip->script_direction === 'rtl' ? 'sh-script-rtl' : '' }}" style="line-height:1.8; margin-top:0.5rem;">
                        {{ $clip->lyrics_input }}
                    </p>
                </div>

            </div>
        </div>

        {{-- Actions --}}
        <div class="sh-card" style="margin-top:1.5rem;">
            <div class="sh-card__header">Actions</div>
            <div class="sh-card__body">

                {{-- Visibility --}}
                <div class="sh-field">
                    <label class="sh-label">Visibility</label>
                    <form method="POST" action="{{ route('studio.visibility', $clip) }}">
                        @csrf
                        @method('PATCH')
                        <select name="visibility" class="sh-select" onchange="this.form.submit()">
                            <option value="private" {{ $clip->visibility === 'private' ? 'selected' : '' }}>Private</option>
                            <option value="public" {{ $clip->visibility === 'public' ? 'selected' : '' }}>Public</option>
                        </select>
                    </form>
                </div>

                {{-- Generate Cover --}}
                <div class="sh-field" style="margin-top:1.5rem;">
                    <label class="sh-label">Generate Cover Image</label>
                    <form method="POST" action="{{ route('studio.cover', $clip) }}">
                        @csrf
                        <input type="text" name="description" class="sh-input"
                               placeholder="Describe the cover (optional)" style="margin-bottom:0.5rem;">
                        <button type="submit" class="sh-btn sh-btn--ghost">Generate Cover</button>
                    </form>
                </div>

                {{-- Create Reel --}}
                <div class="sh-field" style="margin-top:1.5rem;">
                    <label class="sh-label">Create Reel</label>
                    <form method="POST" action="{{ route('studio.reel', $clip) }}">
                        @csrf
                        <button type="submit" class="sh-btn sh-btn--ghost">Create Reel</button>
                    </form>
                </div>

            </div>
        </div>
    @endif

</div>
@endsection
