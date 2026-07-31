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
     * Persist a posting's configuration in one request: its ordered review stages and its
     * item/location watchlist.
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
            // The corporation's required SSO scopes (managed here as the corp 'default' type).
            'selected_scopes' => ['array'],
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

        $this->saveRequiredScopes($corporation_id, $validated['selected_scopes'] ?? []);

        return back()->with('success', 'Posting saved');
    }

    /**
     * Persist (or clear) the corporation's required SSO scopes as the corp 'default' type. Clearing
     * removes the record entirely, so the corporation drops out of the SSO-gated observation list.
     *
     * @param  array<int, string>  $selectedScopes
     */
    private function saveRequiredScopes(int $corporation_id, array $selectedScopes): void
    {
        if ($selectedScopes === []) {
            SsoScopes::query()->where('morphable_id', $corporation_id)->delete();

            return;
        }

        (new UpdateOrCreateSsoSettings([
            'selectedScopes' => $selectedScopes,
            'selectedEntities' => [['id' => $corporation_id, 'category' => 'corporation']],
            'type' => 'default',
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
