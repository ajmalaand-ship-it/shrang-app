@extends('layouts.auth')
@section('title', 'Reset password')
@section('content')
<h1 class="sh-auth-title">Set new password</h1>
<p class="sh-auth-sub">Choose a strong password for your account</p>

@if($errors->any())
    <div class="sh-notice sh-notice--danger">{{ $errors->first() }}</div>
@endif

<form method="POST" action="{{ route('password.update') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <div class="sh-field">
        <label class="sh-label" for="email">Email address</label>
        <input id="email" type="email" name="email" value="{{ old('email', $email ?? '') }}"
               class="sh-input @error('email') sh-input--error @enderror" required autofocus>
        @error('email')<span class="sh-field-error">{{ $message }}</span>@enderror
    </div>
    <div class="sh-field">
        <label class="sh-label" for="password">New password</label>
        <input id="password" type="password" name="password"
               class="sh-input @error('password') sh-input--error @enderror" required>
        @error('password')<span class="sh-field-error">{{ $message }}</span>@enderror
    </div>
    <div class="sh-field">
        <label class="sh-label" for="password_confirmation">Confirm new password</label>
        <input id="password_confirmation" type="password" name="password_confirmation"
               class="sh-input" required>
    </div>
    <button type="submit" class="sh-btn sh-btn--primary sh-btn--full">Reset Password</button>
</form>
<p class="sh-auth-footer"><a href="{{ route('login') }}">Back to log in</a></p>
@endsection
