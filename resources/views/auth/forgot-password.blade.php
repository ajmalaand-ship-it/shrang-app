@extends('layouts.auth')
@section('title', 'Forgot password')
@section('content')
<h1 class="sh-auth-title">Forgot your password?</h1>
<p class="sh-auth-sub">Enter your email and we will send a reset link</p>

@if(session('success'))
    <div class="sh-notice sh-notice--success">{{ session('success') }}</div>
@endif
@if($errors->has('email'))
    <div class="sh-notice sh-notice--danger">{{ $errors->first('email') }}</div>
@endif

<form method="POST" action="{{ route('password.email') }}">
    @csrf
    <div class="sh-field">
        <label class="sh-label" for="email">Email address</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}"
               class="sh-input @error('email') sh-input--error @enderror" required autofocus>
        @error('email')<span class="sh-field-error">{{ $message }}</span>@enderror
    </div>
    <button type="submit" class="sh-btn sh-btn--primary sh-btn--full">Send Reset Link</button>
</form>
<p class="sh-auth-footer"><a href="{{ route('login') }}">Back to log in</a></p>
@endsection
