@extends("layouts.app")
@section("title", "Settings — Admin")
@section("content")
<div class="sh-page-wrap admin-page">
    <header class="sh-section">
        <h1 class="sh-heading">Admin Settings</h1>
    </header>
    @if (session("success"))
        <div class="sh-notice sh-notice--success">{{ session("success") }}</div>
    @endif
    <div class="sh-card">
        <div class="sh-card__body">
            <table class="admin-page__table">
                <thead>
                    <tr><th>Group</th><th>Key</th><th>Label</th><th>Value</th><th></th></tr>
                </thead>
                <tbody>
                    @foreach ($settings as $setting)
                        <tr>
                            <td><span class="sh-badge">{{ $setting->group }}</span></td>
                            <td><code>{{ $setting->key }}</code></td>
                            <td>{{ $setting->label }}</td>
                            <td>
                                <form method="POST" action="{{ route("admin.settings.update") }}" style="display:flex;gap:0.5rem;">
                                    @csrf
                                    <input type="hidden" name="key" value="{{ $setting->key }}">
                                    <input type="text" name="value" class="sh-input"
                                           value="{{ $setting->value }}" style="max-width:160px;">
                                    <button type="submit" class="sh-btn sh-btn--sm sh-btn--primary">Save</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
