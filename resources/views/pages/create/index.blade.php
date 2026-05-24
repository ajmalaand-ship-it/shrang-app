@extends("layouts.app")

@section("title", __("ui.create.heading"))

@section("content")
<div class="sh-page-wrap create-page">

    <header class="sh-section create-page__header">
        <h1 class="sh-heading">{{ __("ui.create.heading") }}</h1>
    </header>

    <div class="sh-card create-page__card">
        <div class="sh-card__body">

            <div id="create-form">
                <div class="sh-field">
                    <label class="sh-label" for="title">{{ __("ui.create.title_label") }}</label>
                    <input id="title" name="title" type="text" class="sh-input" placeholder="My Song">
                </div>

                <div class="sh-field">
                    <label class="sh-label" for="language">{{ __("ui.create.language_label") }}</label>
                    <select id="language" name="language" class="sh-select">
                        @foreach(["ps" => "پښتو", "fa" => "دری", "ur" => "اردو", "ar" => "العربية", "hi" => "हिन्दी", "en" => "English"] as $code => $label)
                            <option value="{{ $code }}" {{ app()->getLocale() === $code ? "selected" : "" }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="sh-field">
                    <label class="sh-label" for="lyrics">{{ __("ui.create.lyrics_label") }}</label>
                    <textarea id="lyrics" name="lyrics" class="sh-textarea" rows="8"
                        placeholder="{{ __("ui.create.lyrics_label") }}"></textarea>
                </div>

                <div class="sh-stack" style="margin-top:1.5rem; gap:1rem; display:flex;">
                    <button id="btn-song" class="sh-btn sh-btn--primary" onclick="createSong()">
                        {{ __("ui.create.song") }}
                    </button>
                    <button id="btn-bed" class="sh-btn sh-btn--ghost" onclick="createBed()">
                        {{ __("ui.create.bed") }}
                    </button>
                </div>
            </div>

            <div id="progress-section" style="display:none;">
                <p class="sh-text-muted" id="progress-message">{{ __("ui.create.generating") }}</p>
                <div class="create-page__progress-bar">
                    <div class="create-page__progress-fill" id="progress-fill" style="width:0%"></div>
                </div>
            </div>

            <div id="error-section" style="display:none;">
                <div class="sh-notice sh-notice--danger" id="error-message"></div>
                <button class="sh-btn sh-btn--ghost" onclick="resetForm()">
                    {{ __("ui.common.retry") }}
                </button>
            </div>

        </div>
    </div>

</div>
@endsection

@section("page_js")
<script>
const csrfToken = document.querySelector("meta[name=csrf-token]").content;

async function createSong() {
    const title    = document.getElementById("title").value;
    const lyrics   = document.getElementById("lyrics").value;
    const language = document.getElementById("language").value;

    if (!lyrics.trim()) {
        alert("Please enter your lyrics first.");
        return;
    }

    showProgress();

    try {
        const response = await fetch("/create/song", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                "Accept": "application/json",
            },
            body: JSON.stringify({ title, lyrics, language }),
        });

        const data = await response.json();

        if (!response.ok) {
            showError(data.error || "Something went wrong.");
            return;
        }

        pollJobStatus(data.job_id, data.clip_id);

    } catch (err) {
        showError("Connection error. Please try again.");
    }
}

async function createBed() {
    alert("Bed music generation coming soon.");
}

function pollJobStatus(jobId, clipId) {
    const interval = setInterval(async () => {
        try {
            const response = await fetch("/api/jobs/" + jobId + "/status", {
                headers: { "Accept": "application/json" }
            });
            const data = await response.json();

            updateProgress(data.progress_pct || 0, data.progress_message || "");

            if (data.status === "done") {
                clearInterval(interval);
                window.location.href = "/studio/" + clipId;
            } else if (data.status === "failed") {
                clearInterval(interval);
                showError(data.error_message || "Generation failed.");
            }
        } catch (err) {
            clearInterval(interval);
            showError("Lost connection. Please check your clips.");
        }
    }, 3000);
}

function showProgress() {
    document.getElementById("create-form").style.display = "none";
    document.getElementById("progress-section").style.display = "block";
    document.getElementById("error-section").style.display = "none";
}

function updateProgress(pct, message) {
    document.getElementById("progress-fill").style.width = pct + "%";
    if (message) document.getElementById("progress-message").textContent = message;
}

function showError(message) {
    document.getElementById("progress-section").style.display = "none";
    document.getElementById("error-section").style.display = "block";
    document.getElementById("error-message").textContent = message;
}

function resetForm() {
    document.getElementById("create-form").style.display = "block";
    document.getElementById("error-section").style.display = "none";
}
</script>
@endsection
