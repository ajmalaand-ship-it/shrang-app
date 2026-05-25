<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Account — Shrang</title>
    
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --orange: #E8732A;
            --orange-light: #F0924F;
            --dark: #0F0E0C;
            --dark-2: #161410;
            --dark-3: #1E1B16;
            --dark-4: #28231C;
            --text: #F5F0E8;
            --text-muted: #9A8E7E;
            --border: #2C2720;
        }
        body {
            background: var(--dark);
            color: var(--text);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }
        .auth-card {
            background: var(--dark-3);
            border: 1px solid var(--border);
            border-radius: 1.25rem;
            padding: 2.5rem;
            width: 100%;
            max-width: 420px;
        }
        .auth-logo {
            text-align: center;
            margin-bottom: 2rem;
            text-decoration: none;
            display: block;
        }
        .auth-logo__arabic {
            font-family: 'Amiri', serif;
            font-size: 2rem;
            color: var(--orange);
            display: block;
        }
        .auth-logo__latin {
            font-size: 1rem;
            color: var(--text-muted);
            letter-spacing: 0.15em;
            text-transform: uppercase;
        }
        .auth-title {
            font-size: 1.375rem;
            font-weight: 600;
            margin-bottom: 0.375rem;
            text-align: center;
        }
        .auth-sub {
            color: var(--text-muted);
            font-size: 0.875rem;
            text-align: center;
            margin-bottom: 2rem;
        }
        .field { margin-bottom: 1.25rem; }
        .field label {
            display: block;
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-bottom: 0.4rem;
            font-weight: 500;
        }
        .field input, .field select {
            width: 100%;
            background: var(--dark-4);
            border: 1px solid var(--border);
            border-radius: 0.625rem;
            padding: 0.75rem 1rem;
            color: var(--text);
            font-size: 0.9375rem;
            font-family: inherit;
            transition: border-color 0.2s;
            outline: none;
        }
        .field input:focus, .field select:focus {
            border-color: var(--orange);
        }
        .field-error {
            color: #e05c5c;
            font-size: 0.8rem;
            margin-top: 0.35rem;
            display: block;
        }
        .btn-primary {
            width: 100%;
            background: var(--orange);
            color: #fff;
            border: none;
            padding: 0.875rem;
            border-radius: 9999px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
            font-family: inherit;
        }
        .btn-primary:hover { background: var(--orange-light); }
        .btn-google {
            width: 100%;
            background: var(--dark-4);
            color: var(--text);
            border: 1px solid var(--border);
            padding: 0.875rem;
            border-radius: 9999px;
            font-size: 0.9375rem;
            font-weight: 500;
            cursor: pointer;
            transition: border-color 0.2s;
            font-family: inherit;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            text-decoration: none;
            margin-bottom: 1.25rem;
        }
        .btn-google:hover { border-color: var(--orange); color: var(--text); }
        .divider {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.25rem;
            color: var(--text-muted);
            font-size: 0.8rem;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }
        .auth-link {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.875rem;
            color: var(--text-muted);
        }
        .auth-link a { color: var(--orange); text-decoration: none; }
        .auth-link a:hover { text-decoration: underline; }
        .notice-success {
            background: rgba(52, 168, 83, 0.1);
            border: 1px solid rgba(52, 168, 83, 0.3);
            color: #4caf50;
            padding: 0.75rem 1rem;
            border-radius: 0.625rem;
            font-size: 0.875rem;
            margin-bottom: 1.25rem;
        }
        .notice-error {
            background: rgba(224, 92, 92, 0.1);
            border: 1px solid rgba(224, 92, 92, 0.3);
            color: #e05c5c;
            padding: 0.75rem 1rem;
            border-radius: 0.625rem;
            font-size: 0.875rem;
            margin-bottom: 1.25rem;
        }
        .remember-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
        }
        .remember-row label { color: var(--text-muted); display: flex; align-items: center; gap: 0.4rem; cursor: pointer; }
        .remember-row a { color: var(--orange); text-decoration: none; }
    </style>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

</head>
<body>
    <div class="auth-card">
        <a href="/" class="auth-logo">
            <span class="auth-logo__arabic">شرنګ</span>
            <span class="auth-logo__latin">Shrang</span>
        </a>
        <h1 class="auth-title">Create your account</h1>
        <p class="auth-sub">Start with 20 free credits</p>

        @if ($errors->any())
            <div class="notice-error">{{ $errors->first() }}</div>
        @endif

        
            <a href="{{ route('auth.google') }}" class="btn-google">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                    <path d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.717v2.258h2.908c1.702-1.567 2.684-3.874 2.684-6.615z" fill="#4285F4"/>
                    <path d="M9 18c2.43 0 4.467-.806 5.956-2.18l-2.908-2.259c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332A8.997 8.997 0 009 18z" fill="#34A853"/>
                    <path d="M3.964 10.71A5.41 5.41 0 013.682 9c0-.593.102-1.17.282-1.71V4.958H.957A8.996 8.996 0 000 9c0 1.452.348 2.827.957 4.042l3.007-2.332z" fill="#FBBC05"/>
                    <path d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0A8.997 8.997 0 00.957 4.958L3.964 6.29C4.672 4.163 6.656 3.58 9 3.58z" fill="#EA4335"/>
                </svg>
                Continue with Google
            </a>
            <div class="divider">or</div>


        <form method="POST" action="{{ route('register.store') }}">
            @csrf
            <div class="field">
                <label for="name">Full name</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus>
                @error('name') <span class="field-error">{{ $message }}</span> @enderror
            </div>
            <div class="field">
                <label for="email">Email address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required>
                @error('email') <span class="field-error">{{ $message }}</span> @enderror
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" required minlength="8">
                @error('password') <span class="field-error">{{ $message }}</span> @enderror
            </div>
            <div class="field">
                <label for="password_confirmation">Confirm password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required>
            </div>
            <div class="field">
                <label for="language">Your preferred language</label>
                <select id="language" name="language">
                    <option value="en">English</option>
                    <option value="ps">پښتو — Pashto</option>
                    <option value="fa">دری — Dari</option>
                    <option value="ur">اردو — Urdu</option>
                    <option value="ar">العربية — Arabic</option>
                    <option value="hi">हिन्दी — Hindi</option>
                </select>
            </div>
            <button type="submit" class="btn-primary" style="margin-top:0.5rem;">Create Account</button>
        </form>

        <p class="auth-link">Already have an account? <a href="{{ route('login') }}">Log in</a></p>
    </div>
</body>
</html>
