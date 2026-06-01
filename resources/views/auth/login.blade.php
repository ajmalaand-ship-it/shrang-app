@extends('layouts.auth')
@section('title', 'Log in')
@section('content')
<h1 class="sh-auth-title">Welcome back</h1>
<p class="sh-auth-sub">Log in to create your music</p>

@if(session('success'))
    <div class="sh-notice sh-notice--success">{{ session('success') }}</div>
@endif
@if($errors->has('email') && !$errors->has('password'))
    <div class="sh-notice sh-notice--danger">{{ $errors->first('email') }}</div>
@endif

<a href="{{ route('auth.google') }}" class="sh-btn--google">
    <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
        <path d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.874 2.684-6.615z" fill="#4285F4"/>
        <path d="M9 18c2.43 0 4.467-.806 5.956-2.18l-2.908-2.259c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332A8.997 8.997 0 009 18z" fill="#34A853"/>
        <path d="M3.964 10.71A5.41 5.41 0 013.682 9c0-.593.102-1.17.282-1.71V4.958H.957A8.996 8.996 0 000 9c0 1.452.348 2.827.957 4.042l3.007-2.332z" fill="#FBBC05"/>
        <path d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0A8.997 8.997 0 00.957 4.958L3.964 6.29C4.672 4.163 6.656 3.58 9 3.58z" fill="#EA4335"/>
    </svg>
    Continue with Google
</a>
<div class="sh-auth-divider">or</div>

<form method="POST" action="{{ route('login.store') }}">
    @csrf
    <div class="sh-field">
        <label class="sh-label" for="email">Email address</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}"
               class="sh-input @error('email') sh-input--error @enderror" required autofocus>
        @error('email')<span class="sh-field-error">{{ $message }}</span>@enderror
    </div>
    <div class="sh-field">
        <label class="sh-label" for="password">Password</label>
        <input id="password" type="password" name="password"
               class="sh-input @error('password') sh-input--error @enderror" required>
        @error('password')<span class="sh-field-error">{{ $message }}</span>@enderror
    </div>
    <div class="sh-auth-remember">
        <label><input type="checkbox" name="remember"> Remember me</label>
        <a href="{{ route('password.request') }}">Forgot password?</a>
    </div>
    <button type="submit" class="sh-btn sh-btn--primary sh-btn--full">Log in</button>
</form>
<p class="sh-auth-footer">Don't have an account? <a href="{{ route('register') }}">Sign up free</a></p>
@endsection
