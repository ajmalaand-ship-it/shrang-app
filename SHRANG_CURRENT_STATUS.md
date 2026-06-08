# Shrang — Current Status & Roadmap to Launch

**This file is the single source of truth.** It is both the live status and the plan. Update it after every confirmed task: move finished items into "DONE LOG" and tick them off the steps below.

**Last updated:** June 8, 2026
**Server:** 157.250.199.106 | **App:** /home/shrang/laravel-app | **Live:** shrang.com
**Stack:** Laravel 13, PHP 8.5, MySQL, Redis, FFmpeg, Google Lyria 3, Imagen 4 (all on Vertex AI)
**Goal:** Public launch as fast as *safely* possible.

---

## CURRENT TASK / NEXT UP

AI Providers architecture + AI Settings tab are DONE (see DONE LOG). Music is working again. Next items, in order:

1. **Open action (Ajmal): request Vertex Lyria 3 allowlist for project `shrang`.** Vertex AI Media Studio (Cloud Console) → request access to the Lyria music models. Confirm the service account has `roles/aiplatform.user`. (IAM is likely NOT the blocker — `lyria-002` reached the model, which would have 403'd on a permission problem — so don't add unnecessary roles.) This unblocks long songs + Shrang languages on Vertex's scalable infra later; approval takes time, so submit early.

2. **Long-song generation (the "up to 3 min" problem).** `dev_pro_180` mode is wired correctly (verified: the " Up to 3 minutes." hint reaches the prompt) but Lyria still returns ~59s. Root cause = Lyria behaviour, NOT a code bug: Lyria has no duration parameter; length is driven by how much musical/lyrical structure the prompt implies. A short lyric + "up to 3 minutes" yields ~60s. FIX = prompt engineering in `PromptService` (buildSongPrompt / bed prompt): add explicit structure (intro, verses, chorus repeats, bridge, outro) and richer arrangement cues to make the model fill the time. Note: even then length is not guaranteed — we improve odds, can't force it. This is a PromptService task, separate from LyriaProvider.

3. Then **Step 2 — Safety Nets** (below) — the next real build phase; launch-blocking.

### Confirmed Lyria diagnosis (verified June 8 — keep for reference)
- **Imagen → Vertex: working.** Keep as-is.
- **Vertex `lyria-002`: WORKING** on project `shrang` via `:predict` + `instances`/`parameters` + Bearer. Confirmed live (returned a 400 "Unsupported language: en" — i.e. reached the model and generated, but **English-only** right now). ~30s. Good as a scalable English test mode only; NOT suitable as the main path since Shrang is Pashto/Dari/Urdu-first.
- **Vertex `lyria-3-pro-preview` / `lyria-3-clip-preview`: NOT accessible** (HTTP 404 = not allowlisted). Exist on Vertex but need access request. Endpoint/format for Vertex Lyria 3 is UNVERIFIED — must test after allowlist before enabling.
- **Developer API Lyria 3 Pro/Clip: WORKING — current real path** for all languages + longer songs + bed music. Its own quota limits.

### Rules (keep)
- Do NOT force all music to Vertex. Do NOT remove Developer API Lyria 3 Pro. Do NOT enable Vertex Lyria 3 until allowlist granted AND endpoint/format tested.

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
