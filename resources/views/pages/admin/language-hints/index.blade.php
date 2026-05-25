@extends('layouts.admin')

@section('title', 'Language Hints — Admin')

@section('content')
<div class="sh-page-wrap admin-lh-page">

    <header class="sh-section">
        <h1 class="sh-heading">Pronunciation Hints</h1>
        <p class="sh-text-muted">
            Words matched in user input inject the prompt_injection text
            into the AI prompt. Cached for 5 minutes per language.
        </p>
    </header>

    @if (session('success'))
        <div class="sh-notice sh-notice--success">{{ session('success') }}</div>
    @endif

    <section class="sh-card sh-section">
        <div class="sh-card__header">Add Hint</div>
        <div class="sh-card__body">
            <form method="POST" action="{{ route('admin.language-hints.store') }}">
                @csrf
                <div class="sh-grid admin-lh-form-grid">
                    <div class="sh-field">
                        <label class="sh-label" for="language_code">Language</label>
                        <select id="language_code" name="language_code" class="sh-select" required>
                            <option value="">— select —</option>
                            @foreach ($languages as $code)
                                <option value="{{ $code }}" @selected(old('language_code') === $code)>
                                    {{ strtoupper($code) }}
                                </option>
                            @endforeach
                        </select>
                        @error('language_code')
                            <span class="sh-field-error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="sh-field">
                        <label class="sh-label" for="word">Word / Phrase</label>
                        <input id="word" name="word" type="text" class="sh-input"
                               value="{{ old('word') }}" required>
                        @error('word')
                            <span class="sh-field-error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="sh-field">
                        <label class="sh-label" for="phoneme_hint">Phoneme Hint</label>
                        <input id="phoneme_hint" name="phoneme_hint" type="text"
                               class="sh-input" value="{{ old('phoneme_hint') }}">
                    </div>
                    <div class="sh-field">
                        <label class="sh-label" for="provider">Provider (optional)</label>
                        <input id="provider" name="provider" type="text" class="sh-input"
                               value="{{ old('provider') }}" placeholder="lyria / elevenlabs">
                    </div>
                </div>
                <div class="sh-field" style="margin-top: 1rem;">
                    <label class="sh-label" for="prompt_injection">Prompt Injection Text</label>
                    <textarea id="prompt_injection" name="prompt_injection"
                              class="sh-textarea" rows="3" required>{{ old('prompt_injection') }}</textarea>
                    @error('prompt_injection')
                        <span class="sh-field-error">{{ $message }}</span>
                    @enderror
                </div>
                <div class="sh-field">
                    <label class="sh-label" for="notes">Notes</label>
                    <textarea id="notes" name="notes" class="sh-textarea"
                              rows="2">{{ old('notes') }}</textarea>
                </div>
                <button type="submit" class="sh-btn sh-btn--primary">Add Hint</button>
            </form>
        </div>
    </section>

    <section class="sh-card sh-section">
        <div class="sh-card__header">
            All Hints
            <span class="sh-badge">{{ $hints->total() }}</span>
        </div>
        <div class="sh-card__body">
            @if ($hints->isEmpty())
                <p class="sh-text-muted">No hints yet.</p>
            @else
                <table class="admin-lh-table">
                    <thead>
                        <tr>
                            <th>Lang</th>
                            <th>Word</th>
                            <th>Phoneme</th>
                            <th>Prompt Injection</th>
                            <th>Provider</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($hints as $hint)
                            <tr>
                                <td><span class="sh-badge sh-badge--lang">{{ strtoupper($hint->language_code) }}</span></td>
                                <td>{{ $hint->word }}</td>
                                <td>{{ $hint->phoneme_hint ?? '—' }}</td>
                                <td class="admin-lh-injection">{{ $hint->prompt_injection }}</td>
                                <td>{{ $hint->provider ?? '—' }}</td>
                                <td>
                                    <form method="POST"
                                          action="{{ route('admin.language-hints.destroy', $hint) }}"
                                          onsubmit="return confirm('Delete this hint?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="sh-btn sh-btn--danger sh-btn--sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div style="margin-top: 1rem;">
                    {{ $hints->links() }}
                </div>
            @endif
        </div>
    </section>

</div>
@endsection
