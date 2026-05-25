@extends('layouts.app')
@section('title', 'My Account')
@section('content')
<div class="sh-page-wrap sh-page-wrap--narrow">

    <div class="account-header" style="margin-bottom:1.5rem;">
        <h1 class="sh-heading">My Account</h1>
        <p class="sh-text-muted">Manage your profile, preferences, and security.</p>
    </div>

    @if(session('success'))
        <div class="sh-notice sh-notice--success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="sh-notice sh-notice--danger">{{ session('error') }}</div>
    @endif

    <div class="sh-card" style="margin-bottom:1.5rem;">
        <div class="sh-card__body" style="display:flex;align-items:center;gap:1.5rem;flex-wrap:wrap;">
            <div style="width:64px;height:64px;border-radius:50%;background:var(--sh-orange);display:flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:700;color:#fff;flex-shrink:0;">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
            <div style="flex:1;">
                <div style="font-size:1.1rem;font-weight:600;">{{ $user->name }}</div>
                <div class="sh-text-muted">{{ $user->email }}</div>
                <div style="display:flex;gap:0.5rem;margin-top:0.5rem;flex-wrap:wrap;">
                    <span class="sh-badge">{{ ucfirst($user->role) }}</span>
                    <span class="sh-badge sh-badge--lang">{{ strtoupper($user->preferred_language) }}</span>
                    <span class="sh-badge">Member since {{ $user->created_at->format('M Y') }}</span>
                </div>
            </div>
            <div style="text-align:center;">
                <div style="font-size:1.5rem;font-weight:700;color:var(--sh-orange);">{{ number_format($user->credit_balance) }}</div>
                <div class="sh-text-muted" style="font-size:0.8rem;">credits</div>
                <a href="{{ route('credits') }}" class="sh-btn sh-btn--primary sh-btn--sm" style="margin-top:0.5rem;">Buy Credits</a>
            </div>
        </div>
    </div>

    <div class="sh-card" style="margin-bottom:1.5rem;">
        <div class="sh-card__header">Profile</div>
        <div class="sh-card__body">
            <form method="POST" action="{{ route('account.profile') }}">
                @csrf
                @method('PATCH')
                <div class="sh-field">
                    <label class="sh-label">Full name</label>
                    <input type="text" name="name" class="sh-input @error('name') sh-input--error @enderror" value="{{ old('name', $user->name) }}">
                    @error('name')<span class="sh-field-error">{{ $message }}</span>@enderror
                </div>
                <div class="sh-field">
                    <label class="sh-label">Email address</label>
                    <input type="email" name="email" class="sh-input @error('email') sh-input--error @enderror" value="{{ old('email', $user->email) }}">
                    @error('email')<span class="sh-field-error">{{ $message }}</span>@enderror
                </div>
                <button type="submit" class="sh-btn sh-btn--primary">Save Profile</button>
            </form>
        </div>
    </div>

    <div class="sh-card" style="margin-bottom:1.5rem;">
        <div class="sh-card__header">Language & Preferences</div>
        <div class="sh-card__body">
            <form method="POST" action="{{ route('account.preferences') }}">
                @csrf
                @method('PATCH')
                <div class="sh-field">
                    <label class="sh-label">Preferred language</label>
                    <select name="preferred_language" class="sh-select">
                        <option value="ps" {{ $user->preferred_language === 'ps' ? 'selected' : '' }}>پښتو — Pashto</option>
                        <option value="fa" {{ $user->preferred_language === 'fa' ? 'selected' : '' }}>دری — Dari/Farsi</option>
                        <option value="ur" {{ $user->preferred_language === 'ur' ? 'selected' : '' }}>اردو — Urdu</option>
                        <option value="ar" {{ $user->preferred_language === 'ar' ? 'selected' : '' }}>العربية — Arabic</option>
                        <option value="hi" {{ $user->preferred_language === 'hi' ? 'selected' : '' }}>हिन्दी — Hindi</option>
                        <option value="en" {{ $user->preferred_language === 'en' ? 'selected' : '' }}>English</option>
                    </select>
                </div>
                <div class="sh-field">
                    <label class="sh-label">Interface locale</label>
                    <select name="locale" class="sh-select">
                        <option value="ps" {{ $user->locale === 'ps' ? 'selected' : '' }}>پښتو — Pashto</option>
                        <option value="fa" {{ $user->locale === 'fa' ? 'selected' : '' }}>دری — Dari/Farsi</option>
                        <option value="ur" {{ $user->locale === 'ur' ? 'selected' : '' }}>اردو — Urdu</option>
                        <option value="ar" {{ $user->locale === 'ar' ? 'selected' : '' }}>العربية — Arabic</option>
                        <option value="hi" {{ $user->locale === 'hi' ? 'selected' : '' }}>हिन्दी — Hindi</option>
                        <option value="en" {{ $user->locale === 'en' ? 'selected' : '' }}>English</option>
                    </select>
                </div>
                <button type="submit" class="sh-btn sh-btn--primary">Save Preferences</button>
            </form>
        </div>
    </div>

    <div class="sh-card" style="margin-bottom:1.5rem;">
        <div class="sh-card__header">Change Password</div>
        <div class="sh-card__body">
            <form method="POST" action="{{ route('account.password') }}">
                @csrf
                @method('PATCH')
                <div class="sh-field">
                    <label class="sh-label">Current password</label>
                    <input type="password" name="current_password" class="sh-input @error('current_password') sh-input--error @enderror">
                    @error('current_password')<span class="sh-field-error">{{ $message }}</span>@enderror
                </div>
                <div class="sh-field">
                    <label class="sh-label">New password</label>
                    <input type="password" name="password" class="sh-input @error('password') sh-input--error @enderror">
                    @error('password')<span class="sh-field-error">{{ $message }}</span>@enderror
                </div>
                <div class="sh-field">
                    <label class="sh-label">Confirm new password</label>
                    <input type="password" name="password_confirmation" class="sh-input">
                </div>
                <button type="submit" class="sh-btn sh-btn--primary">Change Password</button>
            </form>
        </div>
    </div>

    <div class="sh-card" style="border-color:rgba(224,92,92,0.3);margin-bottom:1.5rem;">
        <div class="sh-card__header" style="color:var(--sh-danger);">Delete Account</div>
        <div class="sh-card__body">
            <p class="sh-text-muted" style="margin-bottom:1rem;">Permanently delete your account and all your clips. This cannot be undone.</p>
            <form method="POST" action="{{ route('account.destroy') }}">
                @csrf
                @method('DELETE')
                <div class="sh-field">
                    <label class="sh-label">Enter your password to confirm</label>
                    <input type="password" name="password" class="sh-input @error('password') sh-input--error @enderror">
                    @error('password')<span class="sh-field-error">{{ $message }}</span>@enderror
                </div>
                <button type="submit" class="sh-btn sh-btn--danger" onclick="return confirm('This will permanently delete your account. Are you sure?')">Delete My Account</button>
            </form>
        </div>
    </div>

</div>
@endsection
