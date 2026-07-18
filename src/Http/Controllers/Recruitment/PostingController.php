<?php

namespace Seatplus\Web\Http\Controllers\Recruitment;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Models\Recruitment\Enlistment;
use Seatplus\Web\Services\DispatchCorporationOrAllianceInfoJob;

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

    public function updateStages(Request $request, int $corporation_id): RedirectResponse
    {
        $this->authorizeCorporation($corporation_id);

        $validated = $request->validate([
            'stages' => ['required', 'array', 'min:1'],
            'stages.*.label' => ['required', 'string', 'max:255'],
            'stages.*.role_id' => ['nullable', 'integer', 'exists:roles,id'],
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

        return back()->with('success', 'Review stages updated');
    }

    private function authorizeCorporation(int $corporationId): void
    {
        $user = auth()->user();

        if ($user->can('superuser')) {
            return;
        }

        $manageableIds = $this->getAffiliatedIds->get(
            permissions: [ManageRecruitmentController::MANAGE_PERMISSION],
            corporationRoles: ['Director'],
            user: $user,
        );

        abort_unless(in_array($corporationId, $manageableIds), 403, 'You may not manage this corporation.');
    }
}
