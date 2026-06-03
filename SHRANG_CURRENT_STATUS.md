# Shrang — Current Project Status
**Last updated:** June 2, 2026
**Server:** 157.250.199.106 | **App:** /home/shrang/laravel-app | **Live:** shrang.com
**Stack:** Laravel 13, PHP 8.5, MySQL, Redis, FFmpeg, Google Lyria, Imagen 4

---

## 1. Roadmap Status

| Stage | Name | Status |
|-------|------|--------|
| Stage 0 | Post-Migration Baseline Lock | Complete |
| Stage 1 | Language Foundation and RTL System | Complete |
| Stage 2 | UI Foundation, Brand Consistency, Mobile | IN PROGRESS |
| Stage 3 | Core Creator Journey and Sharing | Complete (gaps noted) |
| Stage 4 | Trust Infrastructure | Complete |
| Stage 5 | Credits and Payments | Complete |
| Stage 6 | SEO and Discoverability | Complete |
| Stage 7 | Admin, Operations, and Cost Control | Complete |
| Stage 8 | Staging Environment and Deployment Safety | Not started |
| Stage 9 | Pashto and Regional Language Quality Depth | Not started |
| Stage 10 | Production Launch Readiness Gate | Not started |

---

## 2. Current Active Stage
Stage 2 - v1.1 branded UI/product-experience cleanup

---

## 3. Completed Stage 2 Tasks

Task 1: Input autofill background fixed + email/password fields forced LTR globally
Task 2: Branded orange checkbox replacing accent-color
Task 3: Shared branded audio player (sh-audio-player) on Public Player and Studio
Task 4: Compact branded file upload on Create page - hidden input + Choose File button + filename display
  - Large drop-zone was REJECTED. Do not return to it.
  - Upload backend still returns Phase 6 pending message - visual branding is done, backend is not.
Task 5A: Native select closed-state chevron arrow via JS wrapper in app.blade.php and public.blade.php
Create page: Emoji icons replaced with orange SVG icons
Discover: Language badges changed from codes (PS/UR) to full names (Pashto/Urdu)
Discover: 50 media_assets cdn_url records updated from staging.shrang.com to shrang.com
Task 6 Discover: no-cover placeholder visually confirmed
  - warm brown #1c1208 background
  - large 120px orange music-note watermark at 15% opacity, absolutely centered
  - overlay made transparent on no-cover cards via .discover-card--no-cover .discover-card__cover-overlay
  - no small centered icon competing with the play button
Task 6 My Clips: visually confirmed - same warm brown #1c1208 + music watermark as Discover

---

## 4. Remaining Stage 2 Tasks (Roadmap Order)

Task 6:  No-cover placeholder My Clips - COMPLETE
Task 7:  Move sharing buttons from Studio to Public Player + restructure Studio workspace actions - COMPLETE
Task 8:  Mobile My Clips single column below 480px - COMPLETE (also applied to Discover)
Task 9:  Admin Discover panel - COMPLETE (cover thumbnails, display_title, compact layout, Remove button visible)
Task 10: MP3 vs Reel download distinction - COMPLETE (SVG icons, smart primary/secondary hierarchy, correct order)
Task 5B: Full custom dropdown open-state (deferred - risky across 17 selects)

---

## 5. Remaining Gaps in Completed Stages

Stage 3 gaps:
- Facebook share on Public Player (replacing Telegram - confirmed in session)
- Public Player page visual redesign - COMPLETE (Vazirmatn font, fixed lyrics alignment, collapsible embed, consistent share grid, Shrang brand mark, polished lyrics card)
- Smart asset hierarchy on Discover - reel preview when reel exists - COMPLETE (desktop hover playback; mobile autoplay deferred; revisit when reels become animated)

Stage 6 gaps:
- OG image reel thumbnail - deferred (reel is MP4, needs FFmpeg frame extraction first)
- Telegram link preview - COMPLETE (added og:site_name and og:image:secure_url; og:image:width/height omitted — cover dimensions vary)

Stage 7 gaps:
- Admin alert when daily spend cap is hit - COMPLETE (danger banner on admin dashboard shows today's spend vs cap)

---

## 6. Stage 8 - Staging Environment (Not Started)

- Recreate staging.shrang.com as separate Laravel clone - own folder, own .env, own database
- Password-protect staging - HTTP basic auth or IP restriction
- Add noindex to staging - X-Robots-Tag header
- Separate queue worker for staging - own systemd service
- Written deployment checklist - pull, migrate, cache clear, worker restart, verify
- Automated daily mysqldump of shrang_staging to /home/shrang/backups/

---

## 7. Stage 9 - Pashto and Regional Language Quality (Not Started)

- Native Pashto speaker testing - at least 10 creations, structured feedback
- Top 50 mispronounced Pashto words added to language_hints database
- ElevenLabs evaluation - test Pashto/Dari vocal quality, document result
- Lyria 3 Pro - retest when Google fixes HTTP 500
- RTL text overlay on reels - Vazirmatn Bold pre-rendered as PNG via FFmpeg
- Dari/Farsi quality - separate evaluation from Pashto
- Urdu Nastaliq rendering - confirm font rendering correctly in player

---

## 8. Stage 10 - Production Launch Readiness Gate (Not Started)

- Error monitoring - Sentry or equivalent, unhandled exceptions alerted
- Laravel Horizon - queue monitoring dashboard, admin-gated
- Terms of service and privacy policy pages - required before public payment processing
- Daily spend cap confirmed active before first public user generates
- APP_DEBUG=false confirmed in production .env
- 5 non-developer test users complete full create to share flow without guidance
- Announcement ready for Pashto/Dari creator community

---

## 9. Rejected Decisions - Do Not Repeat

- Large upload drop-zone on Create page - caused visual artifacts, rejected
- Full custom select open-state dropdown - deferred as Task 5B, too risky across 17 selects
- Subtle gradient placeholder backgrounds - not visible enough, rejected multiple times
- accent-color checkbox - replaced with custom CSS, do not revert

---

## 10. Database Changes Not in Git

- media_assets.cdn_url: 50 records changed from staging.shrang.com to shrang.com on June 2 2026
  This was a one-time tinker migration. Fixed broken cover images on Discover and Studio.

---

## 11. Current Exact Task

CURRENT: Stage 6 and Stage 7 gaps closed. Only remaining gap: Stage 6 OG reel thumbnail (blocked on FFmpeg). Next: Stage 8 (staging environment) or deferred gaps in Section 16.

---

## 12. Decision Rules

- A task is complete only after Ajmal visually or functionally confirms it
- If visual change not visible after deploy and incognito, diagnose HTML/CSS before changing more code
- If approach looks worse than before, record as rejected and do not return to it
- Product result matters more than technical correctness
- Do not move to next task until current task confirmed or explicitly deferred by Ajmal
- Do not commit unfinished visual work unless clearly marked WIP and Ajmal approves

---

## 13. Work Protocol

- One command at a time
- Explain command before running it
- Read file before editing - never edit from memory
- CSS: sync both /home/shrang/public_html/css/shrang.css AND /home/shrang/laravel-app/public/css/shrang.css
- Deploy: bash /home/shrang/laravel-app/deploy.sh after blade changes
- Avoid chained commands with && unless Ajmal approves
- If terminal messy, reset with: pwd then git status --short
- Always check if local branch is ahead of GitHub before pushing
- Push to GitHub only after Ajmal approves
- Start of every session:
    cd /home/shrang/laravel-app
    git status --short
    git log --oneline -15
    cat SHRANG_CURRENT_STATUS.md

---

## 14. Important Open Items

- URGENT: Rotate Resend API key re_h2DGikKS_ - was exposed in prior conversation
- SSH hardening - failed login attempts visible in logs (consider fail2ban or key-only auth)
- Push local commits to GitHub - local main is ahead of origin by many commits - push only after Ajmal approves
- APP_DEBUG must be false in production .env before launch
- Terms of Service and Privacy Policy pages required before public payment processing

---

## 15. Key Files and Technical Reference

| Path | Purpose |
|------|---------|
| /home/shrang/public_html/css/shrang.css | Live CSS - always edit here first |
| /home/shrang/laravel-app/public/css/shrang.css | Laravel public CSS - sync after editing |
| /home/shrang/public_html/css/studio.css | Legacy studio CSS - player rules now in shrang.css |
| /home/shrang/laravel-app/resources/views/pages/ | All blade views |
| /home/shrang/laravel-app/resources/views/layouts/ | Layout files: app, public, auth, admin |
| /home/shrang/laravel-app/deploy.sh | Deploy script - always run after code changes |
| /home/shrang/laravel-app/storage/logs/laravel.log | Laravel error log |
| /home/shrang/laravel-app/SHRANG_CURRENT_STATUS.md | This file - source of truth |
| Database | shrang_staging (MySQL) |
| Queue worker | systemd shrang-worker - queues: ai-generation, default, notifications |
| Branch | main - live at shrang.com |

---

## 16. Revisit Later (Deferred, Not Forgotten)

- Reel preview on Discover: works on desktop hover, but current reels are static single-frame MP4s, so there is no visible motion yet. Revisit when reels become animated — hover preview will then show real motion automatically. Mobile one-visible-reel autoplay (IntersectionObserver) was attempted and deferred; revisit together with animated reels.
- Task 5B: full custom select dropdown open-state across all 17 selects - deferred (risky).
- OG image reel thumbnail (Stage 6): needs FFmpeg frame extraction from the MP4 reel.
- Stage 2 audit leftovers: Like/download buttons small on Discover; Audio/Cover/Reel pills clickability unclear on My Clips; too many badges per card on mobile My Clips; replace play and pinned emoji with SVG.

---

## 17. Session Handoff - How to Resume in a New Chat

A new chat has no memory of past sessions. To resume:
1. Connect: ssh root@157.250.199.106
2. Run:
       cd /home/shrang/laravel-app
       git status --short
       git log --oneline -20
       cat SHRANG_CURRENT_STATUS.md
3. Tell the assistant: "Read SHRANG_CURRENT_STATUS.md fully, then continue from section 11 (Current Exact Task). Follow the Work Protocol in section 13 and Decision Rules in section 12 strictly."

This file is the single source of truth. Keep it updated after every confirmed task.

---

## 18. Session Log

- June 2, 2026 (session 1): Stage 2 Tasks 1-10 complete; SHRANG_CURRENT_STATUS.md created.
- June 2-3, 2026 (session 2): Stage 3 gaps closed - Public Player visual redesign (6 steps: Vazirmatn font, lyrics alignment fix, collapsible embed, consistent share grid, brand mark, polished lyrics card); reel preview on Discover (desktop hover, revisit for animation). Commits: cbfbdb0, c4d9587, 9c52988, 7eaaaaf.
- June 3, 2026 (session 3): Stage 6 Telegram link preview gap closed — added og:site_name and og:image:secure_url to og-meta component. Commit: 7603e33.
- June 3, 2026 (session 3 cont): Stage 7 admin spend-cap alert gap closed — danger banner on admin dashboard when daily cap is hit. Commit: 23f7a11.
