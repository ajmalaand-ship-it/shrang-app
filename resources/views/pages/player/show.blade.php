@extends("layouts.app")

@section("title", $clip->title . " — Shrang")

@section("head_extra")
<x-og-meta
    :title="$clip->title . ' — Shrang'"
    :description="Str::limit($clip->lyrics_input, 160)"
    :imageUrl="$coverUrl ?? ''"
    :pageUrl="request()->url()"
/>
@endsection

@section("content")
<div class="sh-page-wrap player-page">

    <div class="player-page__card sh-card">

        @if ($coverUrl)
            <div class="player-page__cover">
                <img src="{{ $coverUrl }}" alt="{{ $clip->title }}" class="player-page__cover-img">
            </div>
        @endif

        <div class="sh-card__body player-page__body">
            <h1 class="player-page__title">{{ $clip->title }}</h1>

            <div class="sh-badge sh-badge--lang">{{ strtoupper($clip->language) }}</div>

            @if ($audioUrl)
                <div class="player-page__audio">
                    <audio controls class="player-page__audio-player">
                        <source src="{{ $audioUrl }}" type="audio/mpeg">
                        Your browser does not support the audio element.
                    </audio>
                </div>
            @else
                <div class="sh-notice sh-notice--info">
                    Audio not available yet.
                </div>
            @endif

            <div class="player-page__lyrics {{ $clip->script_direction === "rtl" ? "sh-script-rtl" : "" }}">
                {{ $clip->lyrics_input }}
            </div>

            <div class="player-page__actions">
                <button class="sh-btn sh-btn--ghost"
                        onclick="copyLink()">
                    {{ __("ui.common.share") }}
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

@section("page_js")
<script>
function copyLink() {
    navigator.clipboard.writeText(window.location.href).then(() => {
        alert("Link copied to clipboard!");
    });
}
</script>
@endsection
