@extends("layouts.admin")
@section("title", "AI Settings")
@section("content")
<div class="sh-page-wrap admin-page">
    <header class="sh-section">
        <h1 class="sh-heading">AI Settings</h1>
    </header>

    <div class="sh-card" style="margin-bottom:1.5rem;">
        <div class="sh-card__body">
            <p style="margin:0;">
                <strong>Note:</strong> Vertex Lyria 3 Pro generates full multilingual songs with vocals (up to ~3 min) — currently in Google preview, not guaranteed for production. Developer API modes also work and have their own quota. Vertex Lyria 002 is an English-only short test mode.
            </p>
        </div>
    </div>

    <div class="sh-card">
        <div class="sh-card__body">
            <table class="admin-page__table">
                <thead>
                    <tr><th>Setting</th><th>Value</th><th></th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Song music mode</td>
                        <td>
                            <form method="POST" action="{{ route('admin.ai.update') }}" style="display:flex;gap:0.5rem;">
                                @csrf
                                <input type="hidden" name="key" value="lyria_song_mode">
                                <select name="value" class="sh-input" style="max-width:260px;">
                                    <option value="dev_clip_30" @selected($songMode === 'dev_clip_30')>Developer Lyria Clip — 30s</option>
                                    <option value="dev_pro_60" @selected($songMode === 'dev_pro_60')>Developer Lyria Pro — about 60s</option>
                                    <option value="dev_pro_180" @selected($songMode === 'dev_pro_180')>Developer Lyria Pro — up to 3 min</option>
                                    <option value="vertex_002_30" @selected($songMode === 'vertex_002_30')>Vertex Lyria 002 — 30s test mode</option>
                                    <option value="vertex_lyria3_pro" @selected($songMode === 'vertex_lyria3_pro')>Vertex Lyria 3 Pro — full song, vocals (preview)</option>
                                </select>
                                <button type="submit" class="sh-btn sh-btn--sm sh-btn--primary">Save</button>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td>Background music mode</td>
                        <td>
                            <form method="POST" action="{{ route('admin.ai.update') }}" style="display:flex;gap:0.5rem;">
                                @csrf
                                <input type="hidden" name="key" value="lyria_bed_mode">
                                <select name="value" class="sh-input" style="max-width:260px;">
                                    <option value="dev_clip_30" @selected($bedMode === 'dev_clip_30')>Developer Lyria Clip — 30s</option>
                                    <option value="dev_pro_60" @selected($bedMode === 'dev_pro_60')>Developer Lyria Pro — about 60s</option>
                                    <option value="dev_pro_180" @selected($bedMode === 'dev_pro_180')>Developer Lyria Pro — up to 3 min</option>
                                    <option value="vertex_002_30" @selected($bedMode === 'vertex_002_30')>Vertex Lyria 002 — 30s test mode</option>
                                    <option value="vertex_lyria3_pro" @selected($bedMode === 'vertex_lyria3_pro')>Vertex Lyria 3 Pro — full song, vocals (preview)</option>
                                </select>
                                <button type="submit" class="sh-btn sh-btn--sm sh-btn--primary">Save</button>
                            </form>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
