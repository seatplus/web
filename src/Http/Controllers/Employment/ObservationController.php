<?php

namespace Seatplus\Web\Http\Controllers\Employment;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationMemberTracking;
use Seatplus\Web\Http\Actions\Corporation\Recruitment\WatchlistArrayAction;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Resources\EmploymentObservationResource;
use Seatplus\Web\Models\Employment\Employment;
use Seatplus\Web\Services\CharacterInspectionScrollProps;
use Seatplus\Web\Services\GetAffiliatedIds;

/**
 * Observation during employment: the evolution of Member Compliance. Lists a corporation's members
 * (registered users with a character in the corp) together with their SSO-scope compliance, their
 * last in-game activity (from corporation member tracking) and their employment lifecycle status.
 */
class ObservationController extends Controller
{
    final public const string PERMISSION = 'view member compliance';

    public function index(): Response
    {
        /** @var User $user */
        $user = auth()->user();

        // Only corporations that (or whose alliance) have SSO scopes configured are observable — with no
        // scopes there is nothing to be compliant against, so an unconfigured corp (e.g. an old corp the
        // user merely has a character in) would just be noise.
        $corporations = CorporationInfo::query()
            ->has('ssoScopes')
            ->orHas('alliance.ssoScopes')
            ->when(
                ! $user->can('superuser'),
                fn (Builder $query) => $query->whereIn(
                    'corporation_infos.corporation_id',
                    (new GetAffiliatedIds($user))->get(permissions: self::PERMISSION, corporationRoles: 'director'),
                ),
            )
            ->get(['name', 'corporation_id', 'ticker'])
            ->map(fn (CorporationInfo $corporation) => [
                'corporation_id' => $corporation->corporation_id,
                'name' => $corporation->name,
                'ticker' => $corporation->ticker,
                // Server-provided endpoint so the page fetches members via http.js without Ziggy.
                'observe_url' => route('employment.observe.corporation', $corporation->corporation_id),
            ]);

        return Inertia::render('Employment/Index', [
            'corporations' => $corporations,
        ]);
    }

    public function corporation(int $corporation_id): AnonymousResourceCollection
    {
        $this->authorizeCorporation($corporation_id);

        $search = request()->string('search')->toString();

        $users = User::query()
            ->when($search !== '', fn (Builder $query) => $query->whereHas('characters', fn (Builder $query) => $query->where('character_infos.name', 'like', "%{$search}%")))
            ->whereHas('characters.corporation', fn (Builder $query) => $query->where('corporation_infos.corporation_id', $corporation_id))
            ->with([
                'mainCharacter',
                'characters' => fn (Relation $query) => $query->whereHas('corporation', fn (Builder $query) => $query->where('corporation_infos.corporation_id', $corporation_id)),
                'characters.corporation.ssoScopes',
                'characters.alliance.ssoScopes',
                'characters.refreshToken',
            ])
            ->paginate();

        $this->attachObservationData($users->getCollection(), $corporation_id);

        return EmploymentObservationResource::collection($users);
    }

    /**
     * Inspect a single member: their characters (in this corporation) with the shared Asset / Wallet /
     * Contract tabs, fed the same scroll props as the character pages via CharacterInspectionScrollProps.
     */
    public function member(int $corporation_id, User $user, WatchlistArrayAction $watchlist, CharacterInspectionScrollProps $inspection): Response
    {
        $this->authorizeCorporation($corporation_id);

        $user->loadMissing([
            'mainCharacter',
            'characters' => fn (Relation $query) => $query->whereHas('corporation', fn (Builder $query) => $query->where('corporation_infos.corporation_id', $corporation_id)),
        ]);

        $characterIds = $user->characters->pluck('character_id')->map(fn (mixed $id): int => (int) $id)->all();

        return Inertia::render('Employment/Inspect', array_merge([
            'recruit' => $user,
            'watchlist' => $watchlist->execute($corporation_id),
            'targetCorporation' => CorporationInfo::find($corporation_id),
        ], $inspection->build($characterIds, request())));
    }

    private function authorizeCorporation(int $corporation_id): void
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->can('superuser')) {
            return;
        }

        $observableIds = (new GetAffiliatedIds($user))->get(permissions: self::PERMISSION, corporationRoles: 'director');

        abort_unless(in_array($corporation_id, $observableIds), 403, 'You may not observe this corporation.');
    }

    /**
     * Pre-loads the corporation-scoped signals (last logon per character, employment status per user)
     * onto the models so the resource can read them without an N+1 per row.
     *
     * @param  Collection<int, User>  $users
     */
    private function attachObservationData(Collection $users, int $corporation_id): void
    {
        $characterIds = $users->flatMap(fn (User $user) => $user->characters->pluck('character_id'))->all();

        $lastLogonByCharacter = CorporationMemberTracking::query()
            ->where('corporation_id', $corporation_id)
            ->whereIn('character_id', $characterIds)
            ->pluck('logon_date', 'character_id');

        $statusByUser = Employment::query()
            ->where('corporation_id', $corporation_id)
            ->where('subject_type', User::class)
            ->whereIn('subject_id', $users->pluck('id'))
            ->pluck('status', 'subject_id');

        $users->each(function (User $user) use ($lastLogonByCharacter, $statusByUser, $corporation_id) {
            $user->setAttribute('observation_last_logon', $lastLogonByCharacter);
            $user->setAttribute('observation_status', $statusByUser->get($user->getKey()));
            $user->setAttribute('observation_corporation_id', $corporation_id);
        });
    }
}
