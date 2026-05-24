@extends("layouts.app")
@section("title", "Login")
@section("content")
<div class="sh-page-wrap" style="max-width:400px;margin:4rem auto;">
    <div class="sh-card">
        <div class="sh-card__header">Log in to Shrang</div>
        <div class="sh-card__body">
            <form method="POST" action="/login">
                @csrf
                <div class="sh-field">
                    <label class="sh-label">Email</label>
                    <input type="email" name="email" class="sh-input" required>
                </div>
                <div class="sh-field" style="margin-top:1rem;">
                    <label class="sh-label">Password</label>
                    <input type="password" name="password" class="sh-input" required>
                </div>
                <button type="submit" class="sh-btn sh-btn--primary" style="margin-top:1.5rem;">
                    Log in
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
