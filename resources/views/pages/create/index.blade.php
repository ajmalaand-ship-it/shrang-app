@extends('layouts.app')
@section('title', 'Create New')
@section('content')
<div class="sh-page-wrap">

    <div class="sh-section">
        <h1 class="sh-heading">Create New</h1>
        <p class="sh-text-muted">Turn your poetry and lyrics into original AI music</p>
    </div>

    @if ($errors->any())
        <div class="sh-notice sh-notice--danger">{{ $errors->first() }}</div>
    @endif

    @if (session('success'))
        <div class="sh-notice sh-notice--success">{{ session('success') }}</div>
    @endif

    {{-- CREATE SONG --}}
    <div class="sh-card" style="margin-bottom:1.5rem;">
        <div class="sh-card__header">
            <span>🎵 Create Song</span>
            <span class="sh-badge">10 Credits</span>
        </div>
        <div class="sh-card__body">
            <p class="sh-text-muted" style="margin-bottom:1.5rem; font-size:0.875rem;">
                Generate a complete 59–60 second song with vocals, melody, and full musical structure from your lyrics. Perfect for sharing as a reel or social clip.
            </p>
            <form method="POST" action="{{ route('create.song') }}">
                @csrf
                <div class="sh-field" style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                    <div>
                        <label class="sh-label" for="song-title">Song title</label>
                        <input id="song-title" name="title" type="text" class="sh-input" placeholder="My Song" value="{{ old('title') }}">
                    </div>
                    <div>
                        <label class="sh-label" for="song-language">Language</label>
                        <select id="song-language" name="language" class="sh-select" onchange="detectDir('song-lyrics', this.value)">
                            <option value="ps">پښتو — Pashto</option>
                            <option value="fa">دری — Dari</option>
                            <option value="ur">اردو — Urdu</option>
                            <option value="ar">العربية — Arabic</option>
                            <option value="hi">हिन्दी — Hindi</option>
                            <option value="en" selected>English</option>
                        </select>
                    </div>
                </div>
                <div class="sh-field">
                    <label class="sh-label" for="song-lyrics">Your lyrics or poetry</label>
                    <textarea id="song-lyrics" name="lyrics" class="sh-textarea sh-textarea--ltr" rows="7" placeholder="Write your lyrics here...">{{ old('lyrics') }}</textarea>
                    @error('lyrics') <span class="sh-field-error">{{ $message }}</span> @enderror
                </div>
                <button type="submit" class="sh-btn sh-btn--primary sh-btn--full">🎵 Create Song — 10 Credits</button>
            </form>
        </div>
    </div>

    {{-- BACKGROUND MUSIC --}}
    <div class="sh-card" style="margin-bottom:1.5rem;">
        <div class="sh-card__header">
            <span>🎼 Generate Background Music</span>
            <span class="sh-badge">5 Credits</span>
        </div>
        <div class="sh-card__body">
            <p class="sh-text-muted" style="margin-bottom:1.5rem; font-size:0.875rem;">
                Generate up to 3 minutes of instrumental background music inspired by your description or lyrics. No vocals. Perfect for content creators who need original background music for their videos.
            </p>
            <form method="POST" action="{{ route('create.bed') }}">
                @csrf
                <div class="sh-field" style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                    <div>
                        <label class="sh-label" for="bed-title">Title or name</label>
                        <input id="bed-title" name="title" type="text" class="sh-input" placeholder="Calm Pashto Melody">
                    </div>
                    <div>
                        <label class="sh-label" for="bed-language">Language mood</label>
                        <select id="bed-language" name="language" class="sh-select">
                            <option value="ps">Pashto</option>
                            <option value="fa">Dari</option>
                            <option value="ur">Urdu</option>
                            <option value="ar">Arabic</option>
                            <option value="hi">Hindi</option>
                            <option value="en" selected>English</option>
                        </select>
                    </div>
                </div>
                <div class="sh-field">
                    <label class="sh-label" for="bed-desc">Describe the mood or paste lyrics to inspire the music</label>
                    <textarea id="bed-desc" name="description" class="sh-textarea sh-textarea--ltr" rows="5" placeholder="A calm, peaceful Pashto melody with traditional instruments..."></textarea>
                </div>
                <button type="submit" class="sh-btn sh-btn--ghost sh-btn--full" style="border-color:var(--sh-orange); color:var(--sh-orange);">🎼 Generate Background Music — 5 Credits</button>
            </form>
        </div>
    </div>

</div>

<script>
function detectDir(id, lang) {
    const rtl = ['ps','fa','ur','ar'];
    const el = document.getElementById(id);
    if (!el) return;
    if (rtl.includes(lang)) {
        el.classList.remove('sh-textarea--ltr');
        el.classList.add('sh-textarea--rtl');
        el.setAttribute('dir','rtl');
    } else {
        el.classList.remove('sh-textarea--rtl');
        el.classList.add('sh-textarea--ltr');
        el.setAttribute('dir','ltr');
    }
}
</script>
@endsection
