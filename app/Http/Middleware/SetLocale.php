<?php

namespace App\Http\Middleware;

use App\Services\LanguageService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function __construct(private readonly LanguageService $languageService) {}

    public function handle(Request $request, Closure $next): Response
    {
        $locale    = $this->resolveLocale($request);
        $direction = $this->languageService->getDirection($locale);

        App::setLocale($locale);
        Session::put('locale', $locale);

        view()->share('locale', $locale);
        view()->share('direction', $direction->value);
        view()->share('isRtl', $direction->value === 'rtl');

        return $next($request);
    }

    private function resolveLocale(Request $request): string
    {
        if ($request->has('lang')) {
            return $this->languageService->resolve(
                (string) $request->query('lang', 'en')
            );
        }

        if (Auth::check()) {
            $preferred = Auth::user()->preferred_language ?? '';
            if ($preferred !== '') {
                return $this->languageService->resolve($preferred);
            }
        }

        if (Session::has('locale')) {
            return $this->languageService->resolve(
                (string) Session::get('locale')
            );
        }

        return config('app.locale', 'en');
    }
}
