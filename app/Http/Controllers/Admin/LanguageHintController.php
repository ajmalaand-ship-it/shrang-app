<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LanguageHint;
use App\Services\PronunciationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LanguageHintController extends Controller
{
    public function __construct(
        private readonly PronunciationService $pronunciationService
    ) {}

    public function index(): View
    {
        $hints = LanguageHint::query()
            ->orderBy('language_code')
            ->orderBy('word')
            ->paginate(50);

        $languages = ['ps', 'fa', 'ur', 'ar', 'hi', 'en'];

        return view('pages.admin.language-hints.index', compact('hints', 'languages'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'language_code'    => ['required', 'in:ps,fa,ur,ar,hi,en'],
            'word'             => ['required', 'string', 'max:200'],
            'phoneme_hint'     => ['nullable', 'string', 'max:500'],
            'prompt_injection' => ['required', 'string', 'max:1000'],
            'provider'         => ['nullable', 'string', 'max:50'],
            'notes'            => ['nullable', 'string', 'max:1000'],
        ]);

        $validated['is_active']  = true;
        $validated['created_by'] = auth()->id();

        LanguageHint::create($validated);
        $this->pronunciationService->flushCache($validated['language_code']);

        return redirect()
            ->route('admin.language-hints.index')
            ->with('success', 'Hint added.');
    }

    public function destroy(LanguageHint $languageHint): RedirectResponse
    {
        $code = $languageHint->language_code;
        $languageHint->delete();
        $this->pronunciationService->flushCache($code);

        return redirect()
            ->route('admin.language-hints.index')
            ->with('success', 'Hint deleted.');
    }
}
