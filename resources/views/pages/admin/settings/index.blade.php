@extends("layouts.admin")
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
                                    @if (in_array($setting->key, ["lyria_song_mode", "lyria_bed_mode"]))
                                        <select name="value" class="sh-input" style="max-width:180px;">
                                            @foreach (["dev_clip_30" => "Developer Lyria Clip — 30s", "dev_pro_60" => "Developer Lyria Pro — about 60s", "dev_pro_180" => "Developer Lyria Pro — up to 3 min", "vertex_002_30" => "Vertex Lyria 002 — 30s test mode"] as $val => $label)
                                                <option value="{{ $val }}" @selected($setting->value === $val)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input type="text" name="value" class="sh-input" value="{{ $setting->value }}" style="max-width:160px;">
                                    @endif
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
