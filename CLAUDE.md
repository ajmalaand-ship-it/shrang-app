# Shrang — Operating Rules for Claude Code

## 0. READ THIS FIRST, EVERY SESSION
At the start of EVERY session, before doing anything else:
1. Read SHRANG_CURRENT_STATUS.md in this directory, FULLY.
2. It is the single source of truth — where we are, what's done, what's left.
3. Continue from its section 11 (Current Exact Task). Do not invent a task.
4. If anything conflicts with this file, STOP and ask the human (Ajmal).

## 1. PRODUCTION — NO SAFETY NET
- shrang.com is LIVE. Stage 8 staging does NOT exist yet.
- Every change risks the live site. Treat all work as production-critical.

## 2. APPROVAL REQUIRED — NO EXCEPTIONS
- Do NOT edit any file without explicit approval for that specific edit.
- Do NOT run deploy.sh, migrations, or any command without explicit approval.
- Do NOT git commit without approval.
- Do NOT git push — push only after the human explicitly approves.
- Never enable or request auto-accept, skip-permissions, or unattended mode.

## 3. SECRETS — HANDS OFF
- Never read, edit, print, or commit .env or any secret/API key.
- If a task seems to need a secret, STOP and ask.

## 4. WORK PROTOCOL
- One change at a time. Explain BEFORE acting.
- Read a file before editing it — never edit from memory.
- Diagnose before changing code. Stay on the identified issue until the
  human says "works". Do not drift to other issues.
- A task is complete only after the human visually/functionally confirms it.
- If something isn't proven from the files/context, say "not proven" — do not guess.

## 5. CSS RULE
- Edit /home/shrang/public_html/css/shrang.css FIRST.
- Then copy it to /home/shrang/laravel-app/public/css/shrang.css. Keep both in sync.

## 6. DEPLOY (only after approval)
- After Blade changes: bash /home/shrang/laravel-app/deploy.sh — then human tests in incognito.

## 7. ARCHITECTURE RULES
- No AI API calls in controllers. No business logic in Blade views.
- Use services (AI, credits, payments, media, prompts, language/pronunciation).
- Use jobs/queues for AI generation and reel generation.
- Use policies for clip ownership and public/private access.
- No large crowded files; don't mix responsibilities.

## 8. AFTER A CONFIRMED TASK
- Update SHRANG_CURRENT_STATUS.md (with approval), then commit (with approval).
- Push to GitHub only after explicit approval.
