#!/usr/bin/env bash
#
# orca-test-lease.sh — serialize this package's test suite across worktrees.
#
# WHY. `phpunit.xml` pins `DB_DATABASE=laravel_web` with `force="true"` — which is
# non-negotiable, since without it the dev shell's `DB_DATABASE=seatplus` would
# leak in and `migrate:fresh` would wipe the dev database. The consequence is that
# EVERY worktree of this repo runs its tests against the SAME database, whatever
# its own `.env` says. Two parallel `composer run test` runs therefore migrate over
# each other and produce phantom failures that look like real regressions.
#
# The lock lives in the shared git common dir (`git rev-parse --git-common-dir`),
# which every worktree of this repo shares and git never tracks — so it is exactly
# as global as the database it guards.
#
#   ./orca-test-lease.sh composer run test         # run under the lease
#   ./orca-test-lease.sh --wait 0 composer run test   # fail fast if held
#   ./orca-test-lease.sh --status
#   ./orca-test-lease.sh --release                 # drop a leaked lease
#   ./orca-test-lease.sh --release-if <path>       # drop only if <path> holds it
#
# Exit codes: 0 ok (or the command's own status) · 1 usage · 3 lease unavailable.
#
set -euo pipefail

root="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

common="$(git -C "$root" rev-parse --path-format=absolute --git-common-dir 2>/dev/null || true)"
if [[ -z "$common" ]]; then
    echo "error: $root is not a git checkout — cannot locate a shared lock dir" >&2
    exit 1
fi

lock="$common/orca-test-db.lock"
holder_file="$lock/holder"
held_by_us=0
wait_for=300

usage() { grep '^#' "$0" | grep -v '^#!' | sed 's/^# \{0,1\}//'; exit "${1:-0}"; }

field() {
    [[ -f "$holder_file" ]] || return 1
    local value
    value="$(sed -n "s/^$1=//p" "$holder_file" | head -1)"
    [[ -n "$value" ]] && echo "$value"
}

describe_holder() {
    printf '  worktree: %s\n' "$(field path || echo '?')"
    printf '  branch:   %s\n' "$(field branch || echo '?')"
    printf '  pid:      %s on %s\n' "$(field pid || echo '?')" "$(field host || echo '?')"
    printf '  since:    %s\n' "$(field acquired || echo '?')"
}

# A lease is stale when its process is provably gone. Only decidable for locks
# taken on THIS host — a lock from another machine is left alone rather than
# assumed dead. A lock dir with no holder file is a crashed acquire, but only
# after a grace period, so we never break a peer mid-acquire.
is_stale() {
    if [[ ! -f "$holder_file" ]]; then
        [[ -n "$(find "$lock" -maxdepth 0 -mmin +1 2>/dev/null)" ]] && return 0
        return 1
    fi

    [[ "$(field host || echo '?')" == "$(hostname -s 2>/dev/null || echo '?')" ]] || return 1

    local pid
    pid="$(field pid || true)"
    [[ -n "$pid" ]] || return 0

    kill -0 "$pid" 2>/dev/null && return 1
    return 0
}

write_holder() {
    cat >"$holder_file" <<EOF
path=$root
branch=$(git -C "$root" rev-parse --abbrev-ref HEAD 2>/dev/null || echo '?')
pid=$$
host=$(hostname -s 2>/dev/null || echo '?')
acquired=$(date -u +%Y-%m-%dT%H:%M:%SZ)
EOF
}

release_ours() {
    (( held_by_us == 1 )) || return 0
    held_by_us=0
    rm -rf "$lock"
}

acquire() {
    local deadline=$(( SECONDS + wait_for )) announced=0

    while true; do
        if mkdir "$lock" 2>/dev/null; then
            held_by_us=1
            trap release_ours EXIT INT TERM
            write_holder
            return 0
        fi

        if is_stale; then
            echo "note: breaking a stale ${lock##*/} — its holder process is gone" >&2
            describe_holder >&2 || true
            rm -rf "$lock"
            continue
        fi

        if (( announced == 0 )); then
            announced=1
            echo "The laravel_web test database is leased by another worktree:" >&2
            describe_holder >&2
            if (( wait_for > 0 )); then
                echo "  waiting up to ${wait_for}s for it to finish…" >&2
            fi
        fi

        if (( wait_for == 0 )) || (( SECONDS >= deadline )); then
            cat >&2 <<EOF

error: could not take the laravel_web test lease.

Running anyway would migrate:fresh over the other worktree's suite mid-run —
both sides would report failures that are not real.

Options:
  · Wait and retry:      ./orca-test-lease.sh --wait 900 composer run test
  · Do lease-free work:  npm run lint, phpstan, and editing are all parallel-safe.
  · Drop a leaked lease: ./orca-test-lease.sh --release
EOF
            return 3
        fi

        sleep 5
    done
}

case "${1:-}" in
    ""|-h|--help)
        usage 0
        ;;
    --status)
        if [[ ! -d "$lock" ]]; then
            echo "laravel_web test lease: free"
            exit 0
        fi
        echo "laravel_web test lease: HELD"
        describe_holder
        is_stale && echo "  ⚠ stale — the next acquire will break it"
        exit 0
        ;;
    --release)
        rm -rf "$lock"
        echo "✔ laravel_web test lease released"
        exit 0
        ;;
    --release-if)
        want="${2:-}"
        [[ -n "$want" ]] || usage 1
        if [[ -d "$lock" ]] && [[ "$(field path || echo '')" == "$want" ]]; then
            rm -rf "$lock"
            echo "✔ laravel_web test lease released (was held by $want)"
        else
            echo "not the lease holder — nothing to release"
        fi
        exit 0
        ;;
    --wait)
        wait_for="${2:-}"
        [[ "$wait_for" =~ ^[0-9]+$ ]] || usage 1
        shift 2
        ;;
esac

[[ $# -gt 0 ]] || usage 1

acquire || exit 3

"$@"
