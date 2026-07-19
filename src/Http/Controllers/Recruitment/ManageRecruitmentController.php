<?php

namespace Seatplus\Web\Http\Controllers\Recruitment;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Inertia\Inertia;
use Inertia\Response;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Eveapi\Models\SsoScopes;
use Seatplus\Web\Http\Actions\Corporation\Recruitment\WatchedArrayAction;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Models\Recruitment\Enlistment;
use Seatplus\Web\Models\Recruitment\EnlistmentReviewRound;
use Seatplus\Web\Support\CorporationShape;

/**
 * The HR/recruiter workspace, decoupled from the applicant-facing portal. Here a manager opens or
 * closes a posting and configures its structured review stages (each optionally gated by a control
 * group). Gated by the manage permission + director role in the route file.
 */
class ManageRecruitmentController extends Controller
{
    final public const string MANAGE_PERMISSION = 'can open or close corporations for recruitment';

    public function __invoke(): Response
    {
        $user = auth()->user();
        $isSuperuser = $user->can('superuser');

        $manageableIds = $this->getAffiliatedIds->get(
            permissions: [self::MANAGE_PERMISSION],
            corporationRoles: ['Director'],
            user: $user,
        );

        $postings = Enlistment::query()
            ->with([
                'corporation.alliance',
                'corporation.ssoScopes',
                'reviewRounds' => fn (HasMany $query) => $query->orderBy('position'),
            ])
            ->when(! $isSuperuser, fn (Builder $query) => $query->whereIn('corporation_id', $manageableIds))
            ->get()
            ->map(function (Enlistment $posting) {
                /** @var CorporationInfo $corporation */
                $corporation = $posting->corporation;
                $ssoScopes = $corporation->ssoScopes;

                return [
                    'corporation_id' => $posting->corporation_id,
                    'type' => $posting->type,
                    'corporation' => CorporationShape::make($corporation),
                    'stages' => $posting->reviewRounds
                        ->map(fn (EnlistmentReviewRound $round) => [
                            'position' => $round->position,
                            'label' => $round->label,
                            'role_id' => $round->role_id,
                        ])
                        ->values()
                        ->all(),
                    'close_url' => route('recruitment.posting.close', $posting->corporation_id),
                    'save_url' => route('recruitment.posting.save', $posting->corporation_id),
                    // Item/location watchlist for observing applicant (and later employee) assets & contracts.
                    'watched' => (new WatchedArrayAction)->execute($posting->corporation_id),
                    // The corporation's currently required SSO scopes (compliance + application), edited here.
                    'required_scopes' => $ssoScopes instanceof SsoScopes ? ($ssoScopes->selected_scopes ?? []) : [],
                ];
            })
            ->all();

        return Inertia::render('Recruitment/Manage/Index', [
            'postings' => $postings,
            // Control groups available to gate a stage; a stage with no group falls back to the flat
            // recruiter permission.
            'controlGroups' => Role::query()->orderBy('name')->get(['id', 'name']),
            // The full catalogue of ESI scopes a corporation can require, for the scope picker.
            'availableScopes' => config('eveapi.scopes'),
            'openUrl' => route('recruitment.posting.open'),
        ]);
    }
}
