<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
class LoginController extends Controller
{
    public function show(): View
    {
        return view("auth.login");
    }
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            "email"    => ["required", "email"],
            "password" => ["required", "string"],
        ]);
        if (Auth::attempt($validated, $request->boolean("remember"))) {
            $request->session()->regenerate();
            return redirect()->intended(route("create"));
        }
        return back()->withErrors([
            "email" => "These credentials do not match our records.",
        ])->onlyInput("email");
    }
}
