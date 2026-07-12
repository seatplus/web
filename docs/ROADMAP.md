# seatplus/web — Roadmap

Tracks open work, upcoming PRs, and known issues. Update this file when work is started or completed.

---

## Open PRs

### [#1479](https://github.com/seatplus/web/pull/1479) — `CheckAuthorizationWithExtendedScope` middleware
**Branch**: `web/feat/check-auth-extended-scope` → `5.x`  
**Status**: Open, CI passing

Drop-in replacement for `CheckAuthorization` on all 7 character route files. Adds two fallback
authorization paths (only triggered when a single `character_id` is in the route and the primary
`CanUserService` check fails):

1. **Compliance reviewer** (`member compliance: review user`) — can access characters in their
   affiliated compliance scope via `GetCorporationMemberComplianceAffiliatedIdsService`.
2. **Recruiter** (`can accept or deny applications`) — can access characters with an open
   application to their managed corporations via `GetRecruitIdsService`.

Resolves the two `->todo()` stubs in `ComplianceLifeCycleTest` and `RecruitmentLifeCycleTest`.  
Also deletes 3 dead `Pipelines/Check*Pipe.php` files and cleans up PHPStan excludes.

---

## Open Issues (near-term)

### [#1478](https://github.com/seatplus/web/issues/1478) — Allow moderators on opt-in roles
Currently moderators can only be set on `manual` and `on-request` roles. There is no technical
reason to block them on `opt-in` roles. `SetModeratorController` / `RemoveModeratorController`
need the guard updated.

### [#1480](https://github.com/seatplus/web/issues/1480) — `GetAffiliatedIds` DI violation + dead code
`GetAffiliatedIds::get()` creates `new self()` internally, silently discarding the injected
`CanUserService`. Also contains a dead `$this->user` mutation and an unused `?User $user` 3rd
parameter. Surgical fix across 3 files, no behaviour change.

### [#1477](https://github.com/seatplus/web/issues/1477) — Upgrade to Pest 4 *(blocked)*
Blocked by a PHPStan type-coverage bug in `pest-plugin-type-coverage`. Re-evaluate once upstream
releases a fix.

---

## Upcoming work (not yet tracked as issues)

### PR 1.5-J-2 — Frontend Vue components
Implement the Vue pages that the backend controllers now serve:

- `resources/js/Pages/AccessControl/RoleDetail.vue` — currently a bare stub; needs full implementation
- `resources/js/Pages/AccessControl/Types/AutomaticDetail.vue`
- `resources/js/Pages/AccessControl/Types/ManualDetail.vue`
- `resources/js/Pages/AccessControl/Types/OnRequestDetail.vue`
- `resources/js/Pages/AccessControl/Types/OptInDetail.vue`

Reuse the existing `AclTypes/` building blocks (Affiliations, Members, Moderators, Users components).

---

## Frontend modernization — Inertia v3 (in progress)

Rolling the frontend onto native Inertia v3 primitives and off the legacy axios/Ziggy stack.
Three parallel tracks:

- **B1 — InfiniteScroll rollout.** Replace the custom axios-based `InfiniteLoadingHelper`
  with page-level `Inertia::scroll()` props rendered by `<InfiniteScroll>`. Pattern: one
  scroll prop per character/entity, each with a distinct `pageName`, streamed into an
  `items-element` inside a `scroll-region` container.
  - ✅ Character + corporation wallet journals ([#1536](https://github.com/seatplus/web/pull/1536), [#1537](https://github.com/seatplus/web/pull/1537))
  - ✅ Mail ([#1541](https://github.com/seatplus/web/pull/1541))
  - 🔷 Contracts ([#1547](https://github.com/seatplus/web/pull/1547)) — dual-mode `ContractComponent`:
    `scroll-key` prop on the character page, `InfiniteLoadingHelper` fallback kept for the
    recruitment/watchlist endpoint (which still needs the details route).
  - ⏭️ Remaining axios-fed lists (assets, transactions, …) to migrate as the pattern proves out.
- **B2 — Remove axios.** Native `fetch` wrapper at `resources/js/Functions/http.js`
  (`getJson`/`post`, `X-XSRF-TOKEN` from the `XSRF-TOKEN` cookie). Done for the dispatch/update
  sidebar; retire per surface alongside B1.
- **B3 — Remove Ziggy** ([#1462](https://github.com/seatplus/web/issues/1462)). Replace
  `route()` calls with Wayfinder imports from `@/actions/` (controllers) / `@/routes/` (named).
  Wayfinder output is gitignored and generated in CI (`php artisan wayfinder:generate` in
  core's browser job; package "Frontend Lint" is lint-only).

---

## Older open issues (lower priority)

| Issue | Title |
|-------|-------|
| [#1466](https://github.com/seatplus/web/issues/1466) | Open assets missing character_ids? |
| [#1462](https://github.com/seatplus/web/issues/1462) | Remove Ziggy |
| [#1457](https://github.com/seatplus/web/issues/1457) | Asset: open Modal instead of link |
| [#1456](https://github.com/seatplus/web/issues/1456) | Recruitment Update |

---

## Recently completed

| PR | Description |
|----|-------------|
| [#1546](https://github.com/seatplus/web/pull/1546) | `ManualDispatchedJob` `->allowFailures()` — one job's failure no longer cancels the Update batch |
| [#1545](https://github.com/seatplus/web/pull/1545) | Dispatch/update sidebar — owned vs affiliated sections, axios→fetch + Wayfinder, cached `getEntities` |
| [#1544](https://github.com/seatplus/web/pull/1544) | Dispatch sidebar fix (`required_corporation_role` array validation) + owned/affiliated partition + browser tests |
| [#1542](https://github.com/seatplus/web/pull/1542), [#1543](https://github.com/seatplus/web/pull/1543) | `GetEntityFromId` — resolve-id 404 fallback + alliance `[object Object]` name fix |
| [#1541](https://github.com/seatplus/web/pull/1541) | Mail → InfiniteScroll; axios/Ziggy→fetch; fix `app.js` `layout:null` double-sidebar |
| [#1537](https://github.com/seatplus/web/pull/1537), [#1536](https://github.com/seatplus/web/pull/1536) | Wallet journals (char + corp) → Inertia InfiniteScroll |
| [#1476](https://github.com/seatplus/web/pull/1476) | ACL typed controllers — SOLID single-action controllers, new routes, feature tests |
| [#1473](https://github.com/seatplus/web/pull/1473) | Controllers, actions, services, resources refactor (1-C) |
| [#1472](https://github.com/seatplus/web/pull/1472) | Middleware overhaul — remove dead pipeline middleware, fix auth routing (1-B) |
| [#1471](https://github.com/seatplus/web/pull/1471) | Laravel 11 baseline (1-A) |
