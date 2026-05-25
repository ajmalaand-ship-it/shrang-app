<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AdminSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
class GoogleController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver("google")->redirect();
    }
    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver("google")->user();
        } catch (\Exception $e) {
            return redirect()->route("login")->withErrors(["email" => "Google login failed. Please try again."]);
        }
        $bonus = 20;
        try {
            $bonus = app(AdminSettingsService::class)->get("free_tier_bonus", 20);
        } catch (\Exception $e) {}
        $user = User::firstOrCreate(
            ["email" => $googleUser->getEmail()],
            [
                "name"               => $googleUser->getName(),
                "password"           => str()->random(32),
                "preferred_language" => "en",
                "credit_balance"     => $bonus,
                "role"               => "user",
                "is_active"          => true,
            ]
        );
        if (!$user->is_active) {
            return redirect()->route("login")->withErrors(["email" => "Your account has been suspended."]);
        }
        Auth::login($user, true);
        return redirect()->route("create")->with("success", "Welcome to Shrang!");
    }
}
