<?php

namespace Seatplus\Web\Http\Controllers\Recruitment;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Eveapi\Models\SsoScopes;
use Seatplus\Web\Http\Actions\Corporation\Recruitment\UpdateWatchlistAction;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Models\Recruitment\Enlistment;
use Seatplus\Web\Services\DispatchCorporationOrAllianceInfoJob;
use Seatplus\Web\Services\SsoSettings\UpdateOrCreateSsoSettings;

/**
 * HR/recruiter posting management: open or update a posting, close it, and configure its structured
 * review stages. All actions verify the acting manager is affiliated with the target corporation.
 * The route file additionally gates every action behind the manage permission + director role.
 */
class PostingController extends Controller
{
    public function open(Request $request, DispatchCorporationOrAllianceInfoJob $dispatch): RedirectResponse
    {
        $validated = $request->validate([
            // Not constrained to corporation_infos: the corporation may have been found via ESI search
            // and not yet imported (see the on-demand import below).
            'corporation_id' => ['required', 'integer'],
            'type' => ['required', Rule::in(['character', 'user'])],
        ]);

        $corporationId = (int) $validated['corporation_id'];

        $this->authorizeCorporation($corporationId);

        // Pull the corporation in on demand so the posting can display it (name, ticker, alliance).
        if (! CorporationInfo::query()->where('corporation_id', $corporationId)->exists()) {
            $dispatch->handle(CorporationInfo::class, $corporationId);
        }

        $enlistment = Enlistment::query()->updateOrCreate(
            ['corporation_id' => $corporationId],
            ['type' => $validated['type']],
        );

        // Seed a single 'Open' stage so the multi-stage engine always has a round to gate on.
        if ($enlistment->reviewRounds()->count() === 0) {
            $enlistment->reviewRounds()->create([
                'position' => 0,
                'label' => 'Open',
                'role_id' => null,
            ]);
        }

        return back()->with('success', $enlistment->wasRecentlyCreated ? 'Posting opened' : 'Posting updated');
    }

    public function close(int $corporation_id): RedirectResponse
    {
        $this->authorizeCorporation($corporation_id);

        // Cascades to enlistment_review_rounds via the foreign key.
        Enlistment::query()->where('corporation_id', $corporation_id)->delete();

        return back()->with('success', 'Posting closed');
    }

    /**
     * Persist a posting's configuration in one request: its ordered review stages, its item/location
     * watchlist and — the one part that is not posting-local — the corporation's required SSO scopes.
     */
    public function save(Request $request, int $corporation_id, UpdateWatchlistAction $watchlist): RedirectResponse
    {
        $this->authorizeCorporation($corporation_id);

        $validated = $request->validate([
            'stages' => ['required', 'array', 'min:1'],
            'stages.*.label' => ['required', 'string', 'max:255'],
            'stages.*.role_id' => ['nullable', 'integer', 'exists:roles,id'],
            'systems' => ['array'],
            'regions' => ['array'],
            'items' => ['array'],
            'items.*.watchable_id' => ['required'],
            'items.*.watchable_type' => ['required'],
            // The corporation-wide required SSO scopes. 'sometimes' matters: a request that omits the
            // key must leave the corporation's record alone, and only an explicitly submitted empty
            // array clears it. (Were this form ever to carry a file, Inertia would switch to FormData,
            // which drops empty arrays — a clear would then arrive as an omission and no-op.)
            'selected_scopes' => ['sometimes', 'array'],
            'selected_scopes.*' => ['string'],
        ]);

        $enlistment = Enlistment::query()->findOrFail($corporation_id);

        // Replace the whole ordered set — positions are the 0-based index into the submitted list.
        $enlistment->reviewRounds()->delete();

        foreach (array_values($validated['stages']) as $position => $stage) {
            $enlistment->reviewRounds()->create([
                'position' => $position,
                'label' => $stage['label'],
                'role_id' => $stage['role_id'] ?? null,
            ]);
        }

        $watchlist->execute($corporation_id, $validated);

        if (array_key_exists('selected_scopes', $validated)) {
            $this->saveRequiredScopes($corporation_id, array_values($validated['selected_scopes']));
        }

        return back()->with('success', 'Posting saved');
    }

    /**
     * Persist (or clear) the corporation's required SSO scopes.
     *
     * This is not posting-local state. sso_scopes holds at most one row per corporation — both
     * CorporationInfo::ssoScopes() and every reader treat it as a morphOne — and that row is shared
     * with Configuration -> Scopes, member compliance and Observation. A posting save therefore
     * carries the configured requirement level ('type') over rather than forcing it back to
     * 'default', which used to silently downgrade a stricter 'user' requirement.
     *
     * @param  array<int, string>  $selectedScopes
     */
    private function saveRequiredScopes(int $corporation_id, array $selectedScopes): void
    {
        $existing = SsoScopes::query()
            ->where('morphable_id', $corporation_id)
            ->where('morphable_type', CorporationInfo::class)
            ->first();

        if ($selectedScopes === []) {
            // Scoped to this corporation's own row (an alliance may share the id) and deleted through
            // the model, so seatplus/auth's SsoScopeObserver flushes the permission caches — a mass
            // delete skips model events entirely. Keeping an empty row instead is not an option: it
            // would leave the corporation in Observation and member compliance with nothing to check.
            $existing?->delete();

            return;
        }

        (new UpdateOrCreateSsoSettings([
            'selectedScopes' => $selectedScopes,
            'selectedEntities' => [['id' => $corporation_id, 'category' => 'corporation']],
            'type' => $existing instanceof SsoScopes ? $existing->type : 'default',
        ]))->execute();
    }

    private function authorizeCorporation(int $corporationId): void
    {
        $user = auth()->user();

        if ($user->can('superuser')) {
            return;
        }

        $mayManage = $this->getAffiliatedIds->coversCorporation(
            corporationId: $corporationId,
            permissions: [ManageRecruitmentController::MANAGE_PERMISSION],
            corporationRoles: ['Director'],
            user: $user,
        );

        abort_unless($mayManage, 403, 'You may not manage this corporation.');
    }
}
