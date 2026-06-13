# Shrang — Current Status & Roadmap to Launch

**This file is the single source of truth.** It is both the live status and the plan. Update it after every confirmed task: move finished items into "DONE LOG" and tick them off the steps below.

**Last updated:** June 10, 2026
**Server:** 157.250.199.106 | **App:** /home/shrang/laravel-app | **Live:** shrang.com
**Stack:** Laravel 13, PHP 8.5, MySQL, Redis, FFmpeg, Google Lyria 3, Imagen 4 (all on Vertex AI)
**Goal:** Public launch as fast as *safely* possible.

---

## CURRENT TASK / NEXT UP

Core generation is WORKING end-to-end (Lyria 3 on Vertex, multilingual long songs, covers with reliability handling). See DONE LOG. Next items, in order:

1. **Push local commits to GitHub** when ready (review `git log origin/main..HEAD` first — many local commits, nothing pushed yet).

2. **Step 2 — Safety Nets** (below) — the next real build phase; launch-blocking. Includes: transactional credit rollback, double-charge protection, no silent failures. (Cover backoff/retry + duplicate-guard already done — see DONE LOG.)

3. **Pashto / language quality track** (parallel, longest lead time, now unblocked since generation works): native-speaker testing, top-50 pronunciation hints, Dari/Urdu/Hindi rendering, Pashto/Dari music styles.

### Confirmed generation status (verified June 8-9 — keep for reference)
- **Vertex Lyria 3 Pro (`lyria-3-pro-preview`): WORKING & LIVE.** This is the app's core promise delivered. Endpoint: `https://aiplatform.googleapis.com/v1beta1/projects/shrang/locations/global/interactions` (NOT `:predict`; location MUST be `global`). Request: `{"model":"lyria-3-pro-preview","input":[{"type":"text","text":PROMPT}]}`. Response: `outputs[]` array — audio item has `type:audio`, `mime_type:audio/mpeg`, base64 `data` (~5MB MP3); first `type:text` item = lyrics. Confirmed in Pashto (real Pashto vocals, referenced rubab), ~150-170s, up to 184s. Generation takes 60-90s. Google caveat: PUBLIC PREVIEW, "not for production use yet," limited capacity, SynthID watermark. Admin mode = `vertex_lyria3_pro`.
- **Developer API Lyria 3 Pro/Clip: WORKING — alternate path** for all languages + songs + bed music. Its own quota. Admin modes dev_clip_30 / dev_pro_60 / dev_pro_180.
- **Vertex `lyria-002`: WORKING but English-only + instrumental, ~30s.** Test mode only (`vertex_002_30`). NOT for main use (Shrang is Pashto/Dari/Urdu-first).
- **Imagen 4 covers → Vertex (`us-central1`, `:predict`): WORKING. Quota confirmed June 10.** Active path: `CoverController` → `GenerateCoverImageJob` → `GeminiProvider::generateCover()` → Vertex REST `:predict` at `https://{region}-aiplatform.googleapis.com/v1/projects/{project}/locations/{region}/publishers/google/models/imagen-4.0-generate-001:predict`. `.env`: `VERTEX_AI_PROJECT=shrang`, `VERTEX_AI_REGION=us-central1`, `VERTEX_AI_KEY_PATH=/home/shrang/shrang-322f6c4e0f1c.json`. **Confirmed effective per-minute quota** (metric `aiplatform.googleapis.com/online_prediction_requests_per_base_model`, unit `1/min/{project}/{region}/{base_model}`, this is the Agent Platform / Vertex online-prediction quota — NOT the Gemini Developer quota): `imagen-4.0-generate`/us-central1 = **75 RPM**; `imagen-4.0-fast-generate`/us-central1 = **150 RPM**; `imagen-4.0-ultra-generate`/us-central1 = **30 RPM**. Past outages were Dynamic Shared Quota (DSQ) throttling — keep retry/backoff (timeout→rate_limited, 30/60s + jitter, 3 tries) because DSQ/regional capacity can still occasionally throttle under quota. See "Cover quota checkpoint" section below for test results + open items.
- **Song length:** mode-driven now (PromptService reads song mode → 30/60/180s). Fixed the old hardcoded "59-60s". Lyria length still not guaranteed exact, but 3-min mode produces ~3-min songs (live-confirmed 3:01).

### Rules (keep)
- Do NOT force all music to one provider. Keep BOTH Vertex Lyria 3 and Developer API paths. Vertex Lyria 3 is preview — don't treat as production-guaranteed.
- Imagen covers stay on Vertex us-central1 baseline (global also hangs under DSQ — tested; region is NOT the fix).

### Cover quota checkpoint (confirmed June 10, 2026)
**Google quota / account verification:** Alex Cardenal (Google Cloud, Customer Engineer, Startups) submitted an account verification, which adjusts baseline quotas. We verified current quota from Cloud Console + Cloud Shell.
- Correct metric: `aiplatform.googleapis.com/online_prediction_requests_per_base_model`, unit `1/min/{project}/{region}/{base_model}`.
- Confirmed effective limits (us-central1): `imagen-4.0-generate` = **75 RPM**, `imagen-4.0-fast-generate` = **150 RPM**, `imagen-4.0-ultra-generate` = **30 RPM**. All other regions show **Unlimited** for these per-minute rows.
- This is the **Agent Platform / Vertex** online-prediction quota, NOT the Gemini API Developer quota (Gemini rows are separate and lower: imagen-4.0-generate = 10 RPM / 70 per day tier 1).
- **Prior confusion** came from checking the wrong metric (`generate_content_requests_per_minute_per_project_per_base_model`) — that is NOT the metric used by the current Imagen `:predict` cover endpoint.

**Test results after quota confirmation:**
- *Round 1 — sequential* (3 covers, one at a time): **3/3 success.** Logs: 05:28:27 clip `019eaffd-36bf-737a-a5fa-15a7a6446531`; 05:29:07 clip `019eaffc-8bd8-72b4-9089-0005b80796e8`; 05:30:42 clip `019eaff9-0eb0-7132-87b7-7e5ee99cffc8`.
- *Round 2 — light burst* (3 covers close together): **3/3 success.** Logs: 05:45:39 clip `019eb00e-dfc9-714b-b777-ede1a133e0a5`; 05:45:47 clip `019eb00f-8fc9-71b8-b940-0203bf9b8624`; 05:45:55 clip `019eb00f-ec60-718a-808a-6f4db68545f4`.
- **Conclusion:** after verification/quota confirmation, cover generation passed normal sequential + light burst tests; no new timeout / 429 / no-image-data errors in these windows. Enough for current beta-level testing. Keep retry/backoff (DSQ can still occasionally throttle under quota).

**Remaining open questions (quota):**
- Google/Alex still to confirm whether the original **120 RPM** target is approved, pending, or unnecessary.
- Confirm whether Vertex/Agent Platform Imagen has any **daily** limit — Cloud Shell confirmed per-minute only.
- Gemini API image quota rows are separate and lower; do NOT confuse with the Vertex cover path.

### REVISIT LATER (parked — do NOT work on these now)
- **Studio job-status UX truth** (parked, found June 11): after Generate Reel, Studio takes ~2 min to update with no clear feedback — button stuck on "creating reel…", green message flickers/disappears as the page polls every few sec, no notification when done. Reel renders in ~50s (logs); the rest is queue-wait + polling. Same family as the cover-status UI truth issue — fix together as "Studio job-status truth" (clear generating/ready/failed state, stop flicker, notify on done).
- **Cover-status UI truth issue** (parked): the UI can show stale "Your cover image is being generated" after a job has failed or when no active cover job is running. Should show generating only while a job truly runs, a clear failure + retry when failed, never a stuck banner. NOT being worked on for now.
- **Google/Alex quota thread** (parked): confirmation of the 120 RPM target and whether there is a daily limit is on hold. No quota work until we choose to revisit.

### REEL PIPELINE (GenerateReelJob.php) — progress + roadmap
**Phase 1 DONE** (committed): cover-based animated reel, RTL-safe Pango title (no raw drawtext), private temp PNGs, cleanup in finally, downloadable 1080x1920 MP4.
**Phase 2 DONE** (committed 8c5a509, June 11): Reel v2 visual — rounded cover + soft orange glow, gentle floating cover motion (20*sin(t*0.9)), soft bottom gradient fade, clean white Pango title in the fade, smooth duration-aware bg zoom (scale-based, no zoompan/jitter; constant-speed capped min(0.0015*t,0.20)). Cover-glow + title rendered as PNGs inside the job via ImageMagick/Pango. Validated on 2 clips + via real queue. Render ~95-110s for 60s audio.
**Phase 3 — reel DISPLAY across pages (Studio, Discover, Home, Public Player): handle per-page, as part of EACH page's final design pass — NOT now.** Reel generation itself is working/committed; how it appears (primary visual when present, download, mobile playback, posters) gets done when each page is finalized. (Studio job-status UX issue parked in REVISIT LATER.)
**Phase 4 DONE** (committed bce1358, June 11): branded no-cover fallback — warm dark brand background (#2B170C/#140F0B), center Shrang note icon with #E8732A glow, breathing brand-color sound-rings, white RTL-safe Pango title, Ken Burns push-in. Static assets in public/images (nc-still/nc-rings/nc-iglow/shrang-icon). No-cover branch only; cover path untouched; safety fallback if assets missing. NOTE: reel roadmap was re-scoped to Phases 4-12 (see separate roadmap); brand wordmark on clips deferred.
**Phase 5 DONE/DECIDED** (June 11): no length cap. Songs are <=1 min, so reel simply follows audio length (current job already does this via -shortest). No code change needed.
**Phase 6a DONE** (committed affe2a6, June 11): reel template plumbing. ReelController reads optional `template` from request, validates against [cover_glow, minimal_dark, poetry_poster], defaults/falls back to cover_glow, passes into job params. GenerateReelJob has TEMPLATES const + resolves $template at start of handle() (logs it). NO visual change yet — all templates render cover_glow. NEXT 6b: Studio picker UI, then build real minimal_dark + poetry_poster (preview-first).
**Phase 6 DONE** (committed b9ee627 + 80ba3d5, June 11): reel template system.
- Templates: cover_glow (default, unchanged Reel v2), minimal_dark (cover hero on warm charcoal, soft shadow, less orange), poetry_poster (cover-colored ambient wash + big poster title + thin accent line). Built as buildMinimalDark()/buildPoetryPoster() in GenerateReelJob; cover_glow path byte-identical.
- Studio picker: radio chips (sh-radio-group) in the has-cover Create Reel form; cover_glow pre-selected. Controller already validates template.
- No-cover clips: all templates fall back to Phase 4 branded no-cover reel (verified: poetry_poster + no cover => Phase 4 fallback, no error).
- OPEN (minor, deferred): 'Try Again' retry form has no picker (retry uses cover_glow). Decide later.
**Phase 7 REVERTED/DEFERRED** (revert e794a6d, June 12): the Poetry Poster 2nd poem line was removed. Reason: it was STATIC text that does not follow the singing/voice timing — for a 60s multi-line song a fixed second line is not useful and can confuse users. Investigation showed Lyria returns only audio + plain lyrics text + duration (NO per-line/word timing), so accurate synced lyrics is not available for free. Decision: all templates are TITLE ONLY. No synced lyrics, no fake even-split. Real synced lyrics deferred until we have real timing data or a proper alignment plan (Whisper/forced-alignment costs GPU/CPU + new dependency + Pashto-accuracy risk — revisit post-launch). Original (now-reverted) impl detail: Rule: poem line = first non-empty lyrics_input line that is NOT the display_title (handles manual title correctly = uses lyric line 1; no manual title = uses line 2; single-line lyrics or duplicate = title only). RTL-safe Pango PNG, Vazirmatn 40, brand cream #FFD3A8, placed below title in buildPoetryPoster(). Cover_glow + minimal_dark unchanged (title only). Verified 3 cases (no title / manual title / single line) on real clip via temporary-edit-and-restore test.
**Phase 9 DONE** (committed d957c7f + 98b329f, June 13): uploaded video -> reel.
- Upload: new MediaAsset type 'uploaded_video' (migration added it to the type enum). UploadedVideoController upload/destroy mirrors CoverController. Studio has a separate 'Upload Your Own Video' card (mp4/mov/webm, max 100MB) with preview + remove/replace. ClipController passes $uploadedVideo.
- Reel: ReelController accepts source=uploaded_video, finds the uploaded_video asset, dispatches GenerateReelJob with video_path + template=uploaded_video_basic. New buildUploadedVideoBasic() in the job: blurred-fill background + fitted video (no crop/no bars), video MUTED, audio = Shrang clip audio, length follows audio (stream_loop + -shortest = loop short / trim long), subtle bottom fade + RTL-safe Pango title. Output 1080x1920 H.264/AAC faststart. Verified on real clip (h264 1080x1920 + aac, 60.6s).
- Studio 'Create Reel from Uploaded Video' button (source=uploaded_video) reuses studio.reel route.
- DECISIONS: always mute video + use clip audio for now. 'Keep original video audio' (toggle/mix) deferred to a possible Phase 9c. Blurred-fill chosen over center-crop (vertical fills frame so blur invisible there, which is fine; blur shows for horizontal/square). Cover templates + no-cover fallback untouched.
- OPEN (minor): filename not shown next to 'Choose Video' (cosmetic JS); 'Create Reel from Uploaded Video' button still shows after a reel exists.
**Phase 10 DONE** (committed 73b6653, June 13): admin controls for uploaded video.
- Plugged into existing AdminSettings system (AdminSetting model + AdminSettingsService get/set, admin Settings UI lists rows by group). New 'reels' group, 3 keys: upload_video_enabled (bool), upload_video_max_mb (int), upload_video_formats (string mp4,mov,webm). Added to AdminSettingsSeeder for reproducibility.
- UploadedVideoController reads these: blocks upload if disabled; builds mimes + max validation from settings instead of hard-coded 100MB.
- ClipController passes $uploadedVideoEnabled; Studio video card wrapped in @if so it hides when disabled. Settings cached 300s (cache:clear to apply immediately).
- Verified: toggling upload_video_enabled shows/hides the Studio card.
- NOTE: only uploaded-video controls added (no per-template on/off or reel master toggle yet).
**Phase 4 (OLD roadmap)** — template system (Classic Cover+Title, Poetry Poster, Minimal Dark, Afghan Warm, Waveform); admin enable/disable; user picks before generation.
**Phase 5** — better text: optional lyrics/subtitle overlays (PNG/Pango, RTL-safe; timed later).
**Phase 6** — audio-reactive visuals (waveform/bars/subtle pulse).
**Phase 7** — no-cover fallback improvement (branded background, not plain black; encourage cover first).
**Phase 8** — uploaded video support (mute option, combine with generated audio, crop 9:16).
**Phase 9** — reel credit cost (admin-controlled; heavier templates cost more).
**Phase 10** — production polish (thumbnails, watermark by plan, mobile compat, clean old temp/failed, admin monitoring).
**OPTIMIZATION (do when convenient):** cache the static orange-glow template + per-clip glow/title PNGs to cut render time.
**SEPARATE ISSUE (park):** Imagen sometimes bakes unwanted text into covers — fix in cover generation, not reels.

### AI Settings tab (built) — future rows to add later
The dedicated `/admin/ai` page currently has the 2 Lyria mode dropdowns + a note. Later add: image provider control, cover fallback provider (Vertex Imagen → OpenAI), Vertex Imagen status, Vertex Lyria 3 pending/allowlist status, provider notes/limits.

---

## THE SINGLE LAUNCH RULE

Do not open Shrang to the public until ALL are true:
- Generation runs on Vertex AI and survives concurrent users with no silent failures (Steps 1–2). *(Step 1 done; Step 2 pending.)*
- At least one round of native Pashto speaker testing is done (Parallel Track).
- Terms of Service + Privacy Policy live, AND active checkout consent in place (Step 4).
- Private-clip media URLs are not publicly scrapable (Step 4).
- Error monitoring active (Step 4).

---

## DONE LOG (most recent first)

- **June 8-9 — Vertex Lyria 3 Pro LIVE + song length fixed (commit `99a3092`).** Discovered Lyria 3 Pro works on Vertex via the `/interactions` endpoint (location `global`), not `:predict` — earlier 404s were wrong endpoint/format, NOT lack of access. Proven by direct test: full multilingual songs with vocals, ~150-170s, confirmed in Pashto (real Pashto lyrics, rubab). Added provider `vertex_interactions` + `callVertexInteractions()` + `parseInteractionsResponse()` to LyriaProvider; new admin mode `vertex_lyria3_pro` (song + bed dropdowns, validation). Fixed song length: PromptService was hardcoding "59-60 seconds" — now reads the selected song mode's duration (30/60/180) via injected AdminSettingsService. Live-confirmed 3:01 song. Lyria 3 Pro is Google PUBLIC PREVIEW (not production-guaranteed).
- **June 8-9 — Cover generation reliability (commit `ccadc24`).** Diagnosed long cover outage: root cause = Imagen 4 Dynamic Shared Quota (intermittent 429 + connection hangs, independent of own usage; confirmed via Cloud metrics 429 row + Cloud Assist). NOT code/network/region (global endpoint also hangs; tested + reverted to us-central1 baseline). Fixes: (1) GeminiProvider treats cURL timeout/error-28 as `rate_limited` so the job's backoff fires (was dead code); (2) GenerateCoverImageJob backoff + random jitter (30/60s, 3 tries); (3) CoverController duplicate-job guard (no second cover job while one is pending/running — confirmed live); (4) studio view clears the stuck "being generated" banner on failure/timeout, no more banner-forever or generating+failed together (confirmed live). Self-serve Imagen quota increase BLOCKED by Google (capped 10/min until more billing history / contact sales).

- **June 8 — Music RESTORED + provider-aware architecture + AI Settings tab.** Rebuilt `LyriaProvider` to be provider-aware: `resolveMode()` returns provider+model+hint+duration; `callApi()` branches to `callDeveloper()` (generativelanguage + ?key= + generateContent) or `callVertex()` (aiplatform + Bearer + :predict + instances/parameters). Modes: dev_clip_30, dev_pro_60 (song default), dev_pro_180 (bed default), vertex_002_30 (English-only test). Song + bed generation confirmed working via Developer API; Vertex lyria-002 confirmed reachable (English-only). Added dedicated **Admin → AI Settings** page (`/admin/ai`: nav link, AiSettingsController, view with 2 dropdowns + note) and removed all AI rows (lyria_song_mode, lyria_bed_mode, ai_music_provider, song/bed_duration_seconds — all confirmed unused) from the general Settings page via a whereNotIn filter (DB rows kept). Commits: `19ab364` (provider-aware Lyria + validation), `a042ef2` (AI Settings tab). NOT pushed.
- Known issue logged as a task: dev_pro_180 still yields ~59s — Lyria prompt-driven behaviour, fix in PromptService (see Next Up #2).

- **June 8 — Admin music switcher built (UI works), but music generation currently BROKEN.** Added `lyria_song_mode`/`lyria_bed_mode` admin dropdowns (commit pending) and pointed `LyriaProvider` at Vertex. Problem: the switcher sends Developer-API model names (`lyria-3-*`) to the Vertex endpoint, which 404s because the project isn't allowlisted for Lyria 3 on Vertex. Net effect: songs/bed music do not generate right now. Resolution path = the AI Providers architecture in Current Task (Developer API stays for Lyria 3 Pro; Vertex Lyria 3 pending allowlist). **Music is currently down until this is built or the Lyria provider is pointed back at the working Developer API path.**

- **June 7 — STEP 1 COMPLETE: Vertex AI migration.** Both image (`GeminiProvider`, `imagen-4.0-generate-001`) and music (`LyriaProvider`, `lyria-3-pro-preview`) now call `aiplatform.googleapis.com` using a Service Account OAuth Bearer token (key: `/home/shrang/shrang-322f6c4e0f1c.json`, project `shrang`, region `us-central1`). Live-confirmed (cover + song generated) and code-verified. Commits: `6d18abe` (Imagen), `5420f12` (Lyria). The 70/day Developer-API cap is gone.
- June 4 — Cover silent-failure fixed: Studio shows a user notice when `cover_status === "failed"`; clip.status untouched. Commit `bfc1fd1`.
- June 3 — Stage 7 admin spend-cap alert: danger banner on admin dashboard when daily cap hit. Commit `23f7a11`.
- June 3 — Stage 6 Telegram link preview: added `og:site_name` + `og:image:secure_url`. Commit `7603e33`.
- (Earlier) Stages 0–7 of the old roadmap complete: baseline, language/RTL, UI foundation, creator journey + sharing, trust/email, credits + payments (live Stripe confirmed), SEO, admin/cost control.

**Carry-over cleanups (not urgent):** two dead `$baseUrl` constructor lines still reference the old endpoint (harmless); `processLyrics()` text helper still uses the old Developer API key by design.

---

# REMAINING WORK (dependency order — do what unblocks the next thing)

## ✅ STEP 1 — Foundation: Vertex AI Migration — DONE (see Done Log)

## STEP 2 — The Safety Nets
Wrap the working Vertex foundation so it survives load and never double-charges.

- **2.1 Multi-provider cover fallback** — *Large.* `CoverImageProviderInterface`; primary `VertexImagenProvider`, fallback `OpenAIImageProvider`. On a 429/provider error, silently fail over mid-request. If both fail: user sees "Cover generation is temporarily unavailable. Please try again later." Admin controls: enable/disable, priority order, daily cap per provider, cost tracking. Logs: provider used, failure reason, quota errors, cost.
- **2.2 Throttle + smart retries** — *Medium.* Redis throttle (e.g. 2 req / 10s) for image + music; exponential backoff + jitter; fix any status-reset-on-retry that hides failures. *(Note: Lyria Pro is limited to 10 requests/min/region — relevant here.)*
- **2.3 Double-charge protection** — *Medium.* Primary: duplicate-job guard (one pending job per asset per clip) + charge credits only on confirmed success. Secondary/best-effort: idempotency token where the provider honors it (e.g. OpenAI). Note: Google Vertex does NOT dedupe by client token — internal guard is the real protection.
- **2.4 Transactional credit rollback** — *Medium.* On permanent failure, restore credits inside `DB::transaction()`.
- **2.5 No silent failures + cover auto-refresh** — *Quick–Medium.* Every failure shows a clear message; finished cover appears without manual refresh.

**Gate:** silent failover works; concurrency queues cleanly; retries can't double-charge; permanent failure rolls back atomically; no stuck states.

## STEP 3 — Core UX / Product Polish
*(Several captured as notes — plan before coding.)*

**3.0 Cross-page systems (decide once):** one top notification area (Studio + Public Player); card-action model (cards open Public Player, keep only Like, full actions on Player); one shared Like system; reel-preview rule (desktop hover only when stable, mobile poster-first, no native controls in cards).

**3.1 Studio:** Edit/Regenerate Audio (new version, credit warning); Regenerate/Delete Reel; Delete Cover; immediate cover preview; errors to top; action grouping (Main / Edit-Regenerate / Manage Assets / Danger Zone); timeline shows real states.

**3.2 Discover:** revert the unstable reel-preview proof → stable cover/placeholder + Reel badge; then rebuild a STABLE animated preview (custom playback, persistent play button, mobile poster-first). Cards open Public Player.

**3.3 Home:** tighten hero/section spacing; guest vs logged-in CTAs; **returning-user dashboard** (Continue / Create New / My Clips / recent clip); cleaner teaser cards; Shrang-focused "How it works" copy; compact language + pricing sections; footer follows final logo.

**3.4 Public Player:** mobile header check; better copied confirmation; download logic (reel-if-exists, else MP3); confirm WhatsApp/Facebook/OG previews; private = 404; optional Report button; shared Like; **OG reel thumbnail** via FFmpeg frame extraction (reused for Discover/Home posters).

**3.5 Create New:** rename "Generate Bed Music" → "Generate Background Music" (keep code name `bed_music`); keep compact upload. *(Video upload = Step 5.)*

**3.6 Branding:** logo = "Shrang" default, "شرنګ" only on Pashto interface; clean SVG, no emoji; one universal favicon set (.ico, 16/32, apple-touch, android 192/512) wired into all layouts.

**3.7 Carry-over polish:** Stage-2 leftovers (button sizing, pill clickability, mobile badge clutter, emoji→SVG); optional custom select dropdown (Large, not launch-blocking); 375px mobile audit.

## PARALLEL TRACK — Language & Pashto Quality
Starts now that generation is reliable (Step 1 done). Required before launch; longest lead time — start early.
- Native Pashto speaker testing (10–15 creations, written feedback).
- Top-50 mispronounced Pashto words → `language_hints`.
- ElevenLabs eval (Pashto/Dari vocals) — document decision.
- Dari/Farsi eval (separate from Pashto).
- Urdu Nastaliq + Hindi Devanagari rendering confirmed in player.
- Pashto/Dari music styles in Create (ghazal, tapa, loba, nazm, rubai).

## STEP 4 — Launch Gate (Legal, Security, Monitoring)
1. ToS + Privacy pages — **LAUNCH-BLOCKING.**
2. Active checkout consent next to Stripe button — **LAUNCH-BLOCKING.**
3. Direct media-asset protection (signed URLs / auth route for private media) — **LAUNCH-BLOCKING.**
4. Error monitoring (Sentry) — **LAUNCH-BLOCKING.**
5. `APP_DEBUG=false` in production — **LAUNCH-BLOCKING.** *(Currently true — must change.)*
6. Re-confirm live payment end-to-end + webhook idempotency.
7. Confirm real email delivery (not log mode).
8. Laravel Horizon (admin-gated queue dashboard).
9. Daily spend cap active.
10. 5 non-developer test users complete create → share unaided.
11. Announcement ready for Pashto/Dari community.

## STEP 5 — Post-Launch Backlog
Staging environment (do last; meanwhile keep automated daily DB backups); SSH hardening (180+ failed logins — do soon); video upload workflow; reel motion; RTL text overlay on reels; third image provider (Stability/Replicate); subscriptions, community, referrals, Hindi/Urdu depth, CDN.

---

## SECURITY / HOUSEKEEPING (open)
- **Rotate exposed secrets** — Stripe live secret + webhook, DB password, Google OAuth secret, Brevo mail password, Gemini key, Resend key were exposed in a prior chat. Prioritize Stripe.
- `APP_DEBUG=false` before launch (also in Step 4).
- Push local commits to GitHub when ready (currently unpushed: Vertex migration + status updates).

---

## WORK PROTOCOL
- One change at a time; explain before acting; read file before editing.
- Diagnose before changing; stay on the issue until confirmed "works".
- After any front-end/generation change: STOP and let the user test in browser before continuing.
- A task is complete only after the user confirms it.
- Update this file after every confirmed task; commit. Push only after approval.
- Never touch `.env` or secrets; never auto-approve / skip-permissions.
