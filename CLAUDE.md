# CLAUDE.md — seatplus/web

Guidance for Claude Code working in this package on its own (e.g. opened as a
standalone Orca project). A standalone checkout does **not** inherit the core
app's `CLAUDE.md`, skills, or MCP — this file is the local pointer.

## ⚠️ web is NOT runnable on its own — develop it inside core
This is an *optional* Vue 3 + Inertia frontend **package**, not an app: there's no
`artisan`, no app skeleton. You **cannot** `npm run dev`/`build`, generate
Wayfinder `@/actions`, render pages, or run browser tests here — those only exist
in the assembled **core** app. Frontend work belongs in
**[seatplus/core](https://github.com/seatplus/core)** (whose `CLAUDE.md` is the
source of truth), with this package overlaid as a path repo (`composer run
local:on`).

**The live-dev loop (do this in core, not here):**
1. In the core checkout, run `npm run dev`.
2. Edit this package's source. Core resolves it through the `vendor/seatplus/web`
   symlink, and core's `vite.config.js` auto-runs `vendor:publish --tag=web` on any
   `resources/js` change, then HMRs — so your edits appear live without any manual
   publish. See "Working in Orca" in core's `CLAUDE.md` for the two worktree models
   (edit-in-place vs. the `orca-web-worktree.sh` repoint helper).

The browser MCP (`claude-in-chrome`) is global, but only useful against that
running core app.

## What DOES run here
- **Lint:** `npm run lint` (ESLint) — no app needed.
- **PHP unit/feature tests:** `composer run test` (Pint + PHPStan + type-coverage +
  Pest) under Orchestra Testbench. Needs PostgreSQL + Redis. The test DB is
  **`laravel_web`** — a per-package name so this suite runs in parallel with other
  packages' suites — pinned with `force="true"` in `phpunit.xml`; **tests must
  never touch `seatplus`.** Create it once: `createdb laravel_web`.
- **Browser tests** live in `tests/Browser/` but are **excluded** from this
  package's Pest run — they execute only against the assembled core app in core's
  "Browser (vs core)" CI job, which uses core's `laravel` DB (not `laravel_web`).
  They're authored + shipped here, run there.
  Their Playwright driver is a plain **devDependency of this package's
  `package.json`** — which `vendor:publish --tag=web-static` puts at core's
  `package.json`, so core's `npm install` provides it. Never
  `npm install --save-dev playwright` ad hoc in a workflow. The browser *binary*
  is a separate download: `npm run playwright:install`
  (`playwright install --with-deps chromium`).

> See the "Working in Orca" section of core's `CLAUDE.md` for the per-package
> test-DB rationale. Limit: two worktrees of *this* package share `laravel_web`.

## Conventions
Follow the frontend rules in core's `CLAUDE.md` (Inertia v3, Wayfinder, Tailwind
v4, native-fetch `http.js` over axios/Ziggy). New PHP files omit the license
header (match the newest sibling files). See `docs/ROADMAP.md` for in-flight
migrations.

## Shipping work: branch → commit → PR is the default
Finished work lands as a **pull request against `5.x`**, not as uncommitted files
in the worktree. Once a change is complete and verified, commit it on a topic
branch, push, and open the PR with `gh pr create` — no need to ask first. `5.x` is
the main branch; never commit to it directly.
