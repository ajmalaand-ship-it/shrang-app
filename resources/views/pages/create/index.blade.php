@extends('layouts.app')
@section('title', 'Create New — Shrang')
@section('content')
<div class="sh-page-wrap">

    <div class="studio-page__header">
        <h1 class="sh-heading">Create New</h1>
        <p class="sh-text-muted">Choose what you want to create with Shrang.</p>
    </div>

    @if(session('success'))
        <div class="sh-notice sh-notice--success">{{ session('success') }}</div>
    @endif
    @if($errors->has('credits'))
        <div class="sh-notice sh-notice--danger">{{ $errors->first('credits') }}</div>
    @endif
    @if($errors->has('legal'))
        <div class="sh-notice sh-notice--danger">Please confirm the rights statement before generating.</div>
    @endif

    {{-- PATH CARDS --}}
    <div class="create-paths">

        {{-- CREATE SONG --}}
        <div class="create-path" id="path-song">
            <div class="create-path__header" onclick="togglePath('song-form', this)">
                <div class="create-path__info">
                    <span class="create-path__icon">&#127925;</span>
                    <div>
                        <div class="create-path__title">Create Song</div>
                        <div class="create-path__desc">Turn your lyrics or poetry into an original AI song.</div>
                    </div>
                </div>
                <div class="create-path__meta">
                    <span class="sh-badge">10 Credits</span>
                    <span class="create-path__arrow">&#8250;</span>
                </div>
            </div>
            <div class="create-path__form" id="song-form" style="display:none;">
                <form method="POST" action="{{ route('create.song') }}">
                    @csrf
                    {{-- Title --}}
                    <div class="sh-field">
                        <label class="sh-label" for="song-title">Song title <span class="sh-label-opt">(optional)</span></label>
                        <input id="song-title" name="title" type="text" class="sh-input"
                               placeholder="Leave empty to use first line of lyrics"
                               value="{{ old('title') }}">
                    </div>
                    {{-- Language + Style --}}
                    <div class="sh-field sh-field--row">
                        <div>
                            <label class="sh-label" for="song-language">Lyrics language</label>
                            <select id="song-language" name="language" class="sh-select"
                                    onchange="detectDir('song-lyrics', this.value); updateStyles(this.value)">
                                <option value="ps">پښتو — Pashto</option>
                                <option value="fa">دری — Dari</option>
                                <option value="ur">اردو — Urdu</option>
                                <option value="ar">العربية — Arabic</option>
                                <option value="hi">हिन्दी — Hindi</option>
                                <option value="en" selected>English</option>
                            </select>
                        </div>
                        <div>
                            <label class="sh-label" for="song-style">Music style</label>
                            <select id="song-style" name="style" class="sh-select"></select>
                        </div>
                    </div>
                    {{-- Voice --}}
                    <div class="sh-field">
                        <label class="sh-label">Voice preference</label>
                        <div class="sh-radio-group">
                            <label class="sh-radio">
                                <input type="radio" name="voice" value="no_preference" checked> No preference
                            </label>
                            <label class="sh-radio">
                                <input type="radio" name="voice" value="male"> Male vocal
                            </label>
                            <label class="sh-radio">
                                <input type="radio" name="voice" value="female"> Female vocal
                            </label>
                        </div>
                    </div>
                    {{-- Lyrics --}}
                    <div class="sh-field">
                        <label class="sh-label" for="song-lyrics">Lyrics or poetry</label>
                        <textarea id="song-lyrics" name="lyrics" class="sh-textarea sh-textarea--ltr"
                                  rows="7" placeholder="Write your lyrics here...">{{ old('lyrics') }}</textarea>
                        @error('lyrics') <span class="sh-field-error">{{ $message }}</span> @enderror
                    </div>
                    {{-- Creative direction --}}
                    <div class="sh-field">
                        <label class="sh-label" for="song-direction">Creative direction <span class="sh-label-opt">(optional)</span></label>
                        <input id="song-direction" name="creative_direction" type="text" class="sh-input"
                               placeholder="e.g. Make it slow and emotional, use traditional instruments">
                    </div>
                    {{-- Visibility --}}
                    <div class="sh-field">
                        <label class="sh-label">Visibility</label>
                        <div class="sh-radio-group">
                            <label class="sh-radio">
                                <input type="radio" name="visibility" value="private" checked> Private
                            </label>
                            <label class="sh-radio">
                                <input type="radio" name="visibility" value="public"> Public / shareable
                            </label>
                        </div>
                        <p class="sh-field-hint">Public clips are shareable but only appear on Discover after admin approval.</p>
                    </div>
                    {{-- Legal --}}
                    <div class="sh-field">
                        <label class="sh-checkbox">
                            <input type="checkbox" name="legal" value="1" required>
                            <span>I confirm I wrote this text, have permission to use it, or believe it is traditional or public-domain material. I understand Shrang creates an original melody and does not copy any existing singer, song, or recording.</span>
                        </label>
                        @error('legal') <span class="sh-field-error">{{ $message }}</span> @enderror
                    </div>
                    <button type="submit" class="sh-btn sh-btn--primary sh-btn--full">
                        Create Song <span class="sh-btn-badge">10 Credits</span>
                    </button>
                </form>
            </div>
        </div>

        {{-- BED MUSIC --}}
        <div class="create-path" id="path-bed">
            <div class="create-path__header" onclick="togglePath('bed-form', this)">
                <div class="create-path__info">
                    <span class="create-path__icon">&#127908;</span>
                    <div>
                        <div class="create-path__title">Generate Bed Music</div>
                        <div class="create-path__desc">Create instrumental background music for videos, reels, or podcasts.</div>
                    </div>
                </div>
                <div class="create-path__meta">
                    <span class="sh-badge">5 Credits</span>
                    <span class="create-path__arrow">&#8250;</span>
                </div>
            </div>
            <div class="create-path__form" id="bed-form" style="display:none;">
                <form method="POST" action="{{ route('create.bed') }}">
                    @csrf
                    {{-- Title --}}
                    <div class="sh-field">
                        <label class="sh-label" for="bed-title">Music name <span class="sh-label-opt">(optional)</span></label>
                        <input id="bed-title" name="title" type="text" class="sh-input"
                               placeholder="Leave empty for automatic name">
                    </div>
                    {{-- Purpose + Mood --}}
                    <div class="sh-field sh-field--row">
                        <div>
                            <label class="sh-label" for="bed-purpose">Purpose</label>
                            <select id="bed-purpose" name="purpose" class="sh-select">
                                <option value="video background">Video background</option>
                                <option value="podcast intro">Podcast intro</option>
                                <option value="narration bed">Narration bed</option>
                                <option value="emotional scene">Emotional scene</option>
                                <option value="social media reel">Social media reel</option>
                            </select>
                        </div>
                        <div>
                            <label class="sh-label" for="bed-mood">Mood</label>
                            <select id="bed-mood" name="mood" class="sh-select">
                                <option value="calm">Calm</option>
                                <option value="hopeful">Hopeful</option>
                                <option value="cinematic">Cinematic</option>
                                <option value="sad">Sad</option>
                                <option value="motivational">Motivational</option>
                                <option value="dramatic">Dramatic</option>
                            </select>
                        </div>
                    </div>
                    {{-- Language --}}
                    <div class="sh-field">
                        <label class="sh-label" for="bed-language">Cultural/language inspiration</label>
                        <select id="bed-language" name="language" class="sh-select">
                            <option value="ps">Pashto</option>
                            <option value="fa">Dari</option>
                            <option value="ur">Urdu</option>
                            <option value="ar">Arabic</option>
                            <option value="hi">Hindi</option>
                            <option value="en" selected>English / International</option>
                        </select>
                    </div>
                    {{-- Description --}}
                    <div class="sh-field">
                        <label class="sh-label" for="bed-desc">Creative direction <span class="sh-label-opt">(optional)</span></label>
                        <textarea id="bed-desc" name="description" class="sh-textarea" rows="4"
                                  placeholder="e.g. Soft Afghan rubab atmosphere, suitable for documentary narration"></textarea>
                    </div>
                    {{-- Visibility --}}
                    <div class="sh-field">
                        <label class="sh-label">Visibility</label>
                        <div class="sh-radio-group">
                            <label class="sh-radio"><input type="radio" name="visibility" value="private" checked> Private</label>
                            <label class="sh-radio"><input type="radio" name="visibility" value="public"> Public / shareable</label>
                        </div>
                    </div>
                    <button type="submit" class="sh-btn sh-btn--ghost sh-btn--full sh-btn--orange-border">
                        Generate Music <span class="sh-btn-badge">5 Credits</span>
                    </button>
                </form>
            </div>
        </div>

        {{-- UPLOAD AUDIO --}}
        <div class="create-path" id="path-upload">
            <div class="create-path__header" onclick="togglePath('upload-form', this)">
                <div class="create-path__info">
                    <span class="create-path__icon">&#128190;</span>
                    <div>
                        <div class="create-path__title">Upload Audio</div>
                        <div class="create-path__desc">Save your own audio as a Shrang clip.</div>
                    </div>
                </div>
                <div class="create-path__meta">
                    <span class="sh-badge sh-badge--free">Free</span>
                    <span class="create-path__arrow">&#8250;</span>
                </div>
            </div>
            <div class="create-path__form" id="upload-form" style="display:none;">
                <form method="POST" action="{{ route('create.upload') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="sh-field">
                        <label class="sh-label" for="upload-title">Clip title <span class="sh-label-opt">(optional)</span></label>
                        <input id="upload-title" name="title" type="text" class="sh-input" placeholder="My Audio Clip">
                    </div>
                    <div class="sh-field">
                        <label class="sh-label" for="upload-file">Audio file <span class="sh-label-req">MP3 or WAV, max 50MB</span></label>
                        <input id="upload-file" name="audio" type="file" class="sh-input" accept=".mp3,.wav,audio/mpeg,audio/wav">
                        @error('audio') <span class="sh-field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="sh-field">
                        <label class="sh-label">Visibility</label>
                        <div class="sh-radio-group">
                            <label class="sh-radio"><input type="radio" name="visibility" value="private" checked> Private</label>
                            <label class="sh-radio"><input type="radio" name="visibility" value="public"> Public / shareable</label>
                        </div>
                    </div>
                    <button type="submit" class="sh-btn sh-btn--ghost sh-btn--full">Upload Audio</button>
                </form>
            </div>
        </div>

    </div>{{-- end create-paths --}}
</div>

<script>
var styles = {
    ps: {"pashto_folk":"Pashto folk","attan_wedding":"Attan / wedding","rubab_tabla":"Rubab and tabla","slow_ghazal":"Slow Pashto ghazal","sad_migration":"Sad migration song","romantic":"Romantic","patriotic":"Patriotic","modern_emotional":"Modern emotional"},
    fa: {"classical_persian":"Classical Persian","afghan_folk":"Afghan folk","ghazal":"Ghazal","pop":"Modern pop","romantic":"Romantic","sad":"Sad and reflective"},
    ur: {"ghazal":"Ghazal","qawwali":"Qawwali","classical":"Classical","romantic":"Romantic","sad":"Sad","pop":"Modern pop"},
    ar: {"arabic_classical":"Arabic classical","khaleeji":"Khaleeji","romantic":"Romantic","sad":"Sad","pop":"Modern pop"},
    hi: {"bollywood":"Bollywood","classical":"Hindustani classical","folk":"Indian folk","romantic":"Romantic","sad":"Sad and emotional","devotional":"Devotional"},
    en: {"pop":"Pop","folk":"Folk","rnb":"R&B / Soul","cinematic":"Cinematic","acoustic":"Acoustic","sad":"Sad and emotional"}
};
function updateStyles(lang) {
    var sel = document.getElementById('song-style');
    sel.innerHTML = '';
    var opts = styles[lang] || styles.en;
    Object.keys(opts).forEach(function(k){
        var o = document.createElement('option');
        o.value = k; o.textContent = opts[k];
        sel.appendChild(o);
    });
}
function detectDir(id, lang) {
    var rtl = ['ps','fa','ur','ar'];
    var el = document.getElementById(id);
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
function togglePath(formId, header) {
    var form = document.getElementById(formId);
    var arrow = header.querySelector('.create-path__arrow');
    var isOpen = form.style.display !== 'none';
    // close all
    document.querySelectorAll('.create-path__form').forEach(function(f){ f.style.display='none'; });
    document.querySelectorAll('.create-path__arrow').forEach(function(a){ a.style.transform=''; });
    document.querySelectorAll('.create-path').forEach(function(p){ p.classList.remove('create-path--open'); });
    if (!isOpen) {
        form.style.display = 'block';
        arrow.style.transform = 'rotate(90deg)';
        header.closest('.create-path').classList.add('create-path--open');
    }
}
// init styles for default language
updateStyles('en');
// auto-open if there are validation errors
@if($errors->any())
    togglePath('song-form', document.querySelector('#path-song .create-path__header'));
@endif
</script>
@endsection
