<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
class AccountController extends Controller
{
    public function show(): View
    {
        return view('pages.account.index', [
            'user' => auth()->user(),
        ]);
    }
    public function updateProfile(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);
        $user->update($validated);
        return redirect()->route('account')->with('success', 'Profile updated.');
    }
    public function updatePreferences(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'preferred_language' => ['required', 'in:ps,fa,ur,ar,hi,en'],
            'locale'             => ['required', 'in:ps,fa,ur,ar,hi,en'],
        ]);
        auth()->user()->update($validated);
        return redirect()->route('account')->with('success', 'Preferences saved.');
    }
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);
        auth()->user()->update([
            'password' => $validated['password'],
        ]);
        return redirect()->route('account')->with('success', 'Password changed.');
    }
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);
        $user = auth()->user();
        auth()->logout();
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'Your account has been deleted.');
    }
}
