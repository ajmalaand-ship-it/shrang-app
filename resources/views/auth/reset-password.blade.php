<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Set New Password — Shrang</title>
    
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
        <h1 class="auth-title">Set new password</h1>
        <p class="auth-sub">Choose a strong password for your account</p>

        @if ($errors->any())
            <div class="notice-error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="field">
                <label for="email">Email address</label>
                <input id="email" type="email" name="email" value="{{ $email ?? old('email') }}" required>
            </div>
            <div class="field">
                <label for="password">New password</label>
                <input id="password" type="password" name="password" required minlength="8">
            </div>
            <div class="field">
                <label for="password_confirmation">Confirm new password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required>
            </div>
            <button type="submit" class="btn-primary">Reset Password</button>
        </form>
    </div>
</body>
</html>
