# seatplus/web — open work

**GitHub is the source of truth.** Every item below is an issue or a PR; this file
is a hand-maintained snapshot and will drift the moment one of them moves. Check
the live state first:

```bash
gh pr list --state open
gh issue list --state open
```

Snapshot verified **2026-09-08**, against `5.x` at `2696202a`.

## Open PRs

| PR | What |
|----|------|
| [#1697](https://github.com/seatplus/web/pull/1697) | dependabot: bump `brace-expansion` |
| [#1714](https://github.com/seatplus/web/pull/1714) | dependabot: bump `eslint` 10.9.1 → 10.10.0 |

## Open issues

**Tooling / quality**

- [#1649](https://github.com/seatplus/web/issues/1649) — Adopt Rector on tests.
  Blocked on `rector.php` itself: it still targets `LaravelSetList::LARAVEL_100` /
  `SetList::PHP_81` and references rector-1.x classes that moved or were removed
  (`Rector\Core\ValueObject\PhpVersion`, `Rector\Php81\Rector\Array_\FirstClassCallableRector`),
  so it cannot run under the installed rector 2.x. Fix the config first, then add
  `LARAVEL_TYPE_DECLARATIONS` + `pest-plugin-rector`, mirroring seatplus/eveapi#708.
- [#1490](https://github.com/seatplus/web/issues/1490) — Reach and enforce 100%
  line coverage. Type coverage is already at 100% and enforced
  (`composer run test:type-coverage --min=100`); line coverage is neither measured
  nor gated (CI runs `pest --no-coverage --shard=...`).

**UI / behaviour**

- [#1466](https://github.com/seatplus/web/issues/1466) — Open assets missing
  `character_ids`. Reported 2024 against a much older frontend and never
  reproduced since; needs a browser check against current `5.x` before anyone
  writes code.
- [#1457](https://github.com/seatplus/web/issues/1457) — Assets: open a modal
  instead of navigating away.
- [#1456](https://github.com/seatplus/web/issues/1456) — Recruitment update. Only
  one box is left unticked: show an explicit empty state when the location list
  has no entries.

**Long-standing backlog** (open since 2020/2021, no active work)

- [#223](https://github.com/seatplus/web/issues/223) — let users dispatch their own
  updates. Single-resource updates are done; per-character updates are not.
- [#892](https://github.com/seatplus/web/issues/892) — improve character adding
  (async list, only show available characters).
- [#893](https://github.com/seatplus/web/issues/893) — account bags (group up to
  three characters).

## Finished since the last revision of this file

The previous version tracked the Laravel-11 / ACL / Inertia-v3 push. All of it has
landed, which is why none of it appears above:

- The frontend modernization tracks are complete. `axios`, `ziggy-js` and
  `InfiniteLoadingHelper` are gone from `resources/js` (the only remaining hits are
  comments recording what replaced them): lists use `Inertia::scroll()` with
  `<InfiniteScroll>`, one-off requests use the native-fetch wrapper
  `resources/js/Functions/http.js`, and URLs come from Wayfinder. [#1462](https://github.com/seatplus/web/issues/1462)
  (remove Ziggy) was closed as not-planned once nothing was left to remove.
- `CheckAuthorizationWithExtendedScope` shipped ([#1479](https://github.com/seatplus/web/pull/1479)),
  as did moderators on opt-in roles ([#1478](https://github.com/seatplus/web/issues/1478))
  and the `GetAffiliatedIds` DI fix ([#1480](https://github.com/seatplus/web/issues/1480)).
- The SSO-settings `TypeError` ([#1387](https://github.com/seatplus/web/issues/1387))
  is fixed: [#1637](https://github.com/seatplus/web/pull/1637) merged 2026-09-08,
  repairing the scope-settings edit screen and surfacing skipped entities instead of
  dropping them silently.
- [#1477](https://github.com/seatplus/web/issues/1477) ("upgrade to Pest 4,
  blocked") is obsolete: the repo runs **Pest 5** with PHPUnit 13 on PHP 8.5 /
  Laravel 13 ([#1489](https://github.com/seatplus/web/issues/1489),
  [#1647](https://github.com/seatplus/web/issues/1647)), and PHPStan sits at level 5
  with 100% type coverage.
