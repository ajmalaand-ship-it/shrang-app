@extends("layouts.app")

@section("title", __("ui.studio.heading"))

@section("content")
<div class="sh-page-wrap studio-page">

    <header class="sh-section studio-page__header">
        <h1 class="sh-heading">{{ __("ui.studio.heading") }}</h1>
        <p class="sh-text-muted">{{ $clip->title }}</p>
    </header>

    @if (session("success"))
        <div class="sh-notice sh-notice--success">{{ session("success") }}</div>
    @endif

    @if (session("error"))
        <div class="sh-notice sh-notice--danger">{{ session("error") }}</div>
    @endif

    <div class="studio-page__grid">

        {{-- Clip info card --}}
        <div class="sh-card studio-page__main">
            <div class="sh-card__header">
                {{ $clip->title }}
                <span class="sh-badge sh-badge--lang">{{ strtoupper($clip->language) }}</span>
                <span class="sh-badge sh-badge--status">{{ $clip->status }}</span>
            </div>
            <div class="sh-card__body">

                @if ($clip->status === "ready")
                    <div class="studio-page__player">
                        <p class="sh-text-muted">Audio player coming in Phase 8.</p>
                    </div>
                @elseif ($clip->status === "processing")
                    <div class="sh-notice sh-notice--info">
                        Your song is still being generated. Please check back in a moment.
                    </div>
                @elseif ($clip->status === "failed")
                    <div class="sh-notice sh-notice--danger">
                        Generation failed. Please try creating a new song.
                    </div>
                @else
                    <div class="sh-notice sh-notice--info">
                        Status: {{ $clip->status }}
                    </div>
                @endif

                <div class="studio-page__lyrics">
                    <h3 class="sh-label">Lyrics</h3>
                    <p class="{{ $clip->script_direction === "rtl" ? "sh-script-rtl" : "" }}">
                        {{ $clip->lyrics_input }}
                    </p>
                </div>

            </div>
        </div>

        {{-- Actions card --}}
        <div class="sh-card studio-page__actions">
            <div class="sh-card__header">{{ __("ui.studio.heading") }} Actions</div>
            <div class="sh-card__body">

                {{-- Visibility toggle --}}
                <div class="sh-field">
                    <label class="sh-label">{{ __("ui.studio.visibility") }}</label>
                    <form method="POST" action="{{ route("studio.visibility", $clip) }}">
                        @csrf
                        @method("PATCH")
                        <select name="visibility" class="sh-select" onchange="this.form.submit()">
                            <option value="private" {{ $clip->visibility === "private" ? "selected" : "" }}>
                                {{ __("ui.studio.private") }}
                            </option>
                            <option value="public" {{ $clip->visibility === "public" ? "selected" : "" }}>
                                {{ __("ui.studio.public") }}
                            </option>
                        </select>
                    </form>
                </div>

                {{-- Cover generation --}}
                <div class="sh-field" style="margin-top:1.5rem;">
                    <label class="sh-label">{{ __("ui.studio.cover") }}</label>
                    <form method="POST" action="{{ route("studio.cover", $clip) }}">
                        @csrf
                        <input type="text" name="description" class="sh-input"
                               placeholder="Describe the cover (optional)">
                        <button type="submit" class="sh-btn sh-btn--ghost" style="margin-top:0.5rem;">
                            {{ __("ui.studio.cover") }}
                        </button>
                    </form>
                </div>

                {{-- Reel generation --}}
                <div class="sh-field" style="margin-top:1.5rem;">
                    <label class="sh-label">{{ __("ui.studio.reel") }}</label>
                    <form method="POST" action="{{ route("studio.reel", $clip) }}">
                        @csrf
                        <button type="submit" class="sh-btn sh-btn--ghost">
                            {{ __("ui.studio.reel") }}
                        </button>
                    </form>
                </div>

                {{-- Share / Download --}}
                <div class="sh-field" style="margin-top:1.5rem;">
                    <label class="sh-label">{{ __("ui.common.share") }}</label>
                    <p class="sh-text-muted">Share and download coming in Phase 8.</p>
                </div>

            </div>
        </div>

    </div>

</div>
@endsection
