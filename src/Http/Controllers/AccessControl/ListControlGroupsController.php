<?php


namespace Seatplus\Web\Http\Controllers\AccessControl;


use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Eveapi\Models\Alliance\AllianceInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Resources\RoleRessource;

class ListControlGroupsController extends Controller
{
    public function __invoke()
    {
        $character_ids = auth()->user()->characters->map(fn ($character) => $character->character_id)->toArray();

        $query = Role::with('moderators.affiliatable.characters', 'acl_members')
            ->when(auth()->user()->can('superuser'),
                // Condition if user has superuser
                fn ($query) => $query->orWhereNotIn('id', []),
                // if user does not have superuser
                fn ($query) => $query
                    ->whereHas('members', fn ($query) => $query->whereUserId(auth()->user()->getAuthIdentifier()))
                    ->orWhereHas('acl_affiliations', fn ($query) => $query->whereHasMorph('affiliatable',
                        [CorporationInfo::class, AllianceInfo::class],
                        fn ($query) => $query->whereHas('characters', fn ($query) => $query->whereIn('character_infos.character_id', $character_ids))
                    ))
            );

        return RoleRessource::collection(
            $query->paginate()
        );
    }

}
