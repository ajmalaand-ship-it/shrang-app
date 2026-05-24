<?php

namespace App\Http\Controllers;

use App\Services\LanguageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function __construct(private readonly LanguageService $languageService) {}

    public function switch(Request $request, string $code): RedirectResponse
    {
        $locale = $this->languageService->resolve($code);

        Session::put('locale', $locale);

        if (Auth::check()) {
            Auth::user()->update(['preferred_language' => $locale]);
        }

        return redirect()
            ->back(fallback: '/')
            ->withHeaders(['Cache-Control' => 'no-store, no-cache']);
    }
}
