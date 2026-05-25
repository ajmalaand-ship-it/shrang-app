<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AdminSettingsService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
class RegisterController extends Controller
{
    public function show(): View
    {
        return view("auth.register");
    }
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            "name"     => ["required", "string", "max:100"],
            "email"    => ["required", "email", "max:200", "unique:users,email"],
            "password" => ["required", "string", "min:8", "confirmed"],
            "language" => ["nullable", "in:ps,fa,ur,ar,hi,en"],
        ]);
        $bonus = 20;
        try {
            $bonus = app(AdminSettingsService::class)->get("free_tier_bonus", 20);
        } catch (\Exception $e) {}
        $user = User::create([
            "name"               => $validated["name"],
            "email"              => $validated["email"],
            "password"           => $validated["password"],
            "preferred_language" => $validated["language"] ?? "en",
            "credit_balance"     => $bonus,
            "role"               => "user",
            "is_active"          => true,
        ]);
        Auth::login($user);
        return redirect()->route("create")->with("success", "Welcome to Shrang! You have " . $bonus . " free credits.");
    }
}
