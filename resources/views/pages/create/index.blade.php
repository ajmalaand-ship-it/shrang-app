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

    <div class="sh-card">
        <div class="sh-card__header">
            Create Song
            <span class="sh-badge">10 credits</span>
        </div>
        <div class="sh-card__body">
            <form method="POST" action="{{ route('create.song') }}">
                @csrf
                <div class="sh-field">
                    <label class="sh-label" for="title">Song title</label>
                    <input id="title" name="title" type="text" class="sh-input"
                           placeholder="My Song" value="{{ old('title') }}">
                </div>
                <div class="sh-field">
                    <label class="sh-label" for="language">Language</label>
                    <select id="language" name="language" class="sh-select">
                        <option value="ps" {{ old('language') === 'ps' ? 'selected' : '' }}>پښتو — Pashto</option>
                        <option value="fa" {{ old('language') === 'fa' ? 'selected' : '' }}>دری — Dari</option>
                        <option value="ur" {{ old('language') === 'ur' ? 'selected' : '' }}>اردو — Urdu</option>
                        <option value="ar" {{ old('language') === 'ar' ? 'selected' : '' }}>العربية — Arabic</option>
                        <option value="hi" {{ old('language') === 'hi' ? 'selected' : '' }}>हिन्दी — Hindi</option>
                        <option value="en" {{ old('language', 'en') === 'en' ? 'selected' : '' }}>English</option>
                    </select>
                </div>
                <div class="sh-field">
                    <label class="sh-label" for="lyrics">Your lyrics or poetry</label>
                    <textarea id="lyrics" name="lyrics" class="sh-textarea" rows="8"
                              placeholder="Write your lyrics here...">{{ old('lyrics') }}</textarea>
                    @error('lyrics')
                        <span class="sh-field-error">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="sh-btn sh-btn--primary">
                    Create Song — 10 credits
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
