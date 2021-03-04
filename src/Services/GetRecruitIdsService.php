<?php


namespace Seatplus\Web\Services;


use Illuminate\Database\Eloquent\Relations\MorphTo;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Application;

class GetRecruitIdsService
{
    public static function get() : array
    {
        $recruiter_permission = 'can accept or deny applications';

        if (! auth()->user()->can($recruiter_permission)) {
            return [];
        }

        return Application::whereIn('corporation_id', getAffiliatedIdsByPermission($recruiter_permission))
            ->whereStatus('open')
            ->with([
                'applicationable' => fn (MorphTo $morph_to) => $morph_to->morphWith([
                    User::class => ['characters'],
                ]),
            ])
            ->get()
            ->map(fn ($recruit) => $recruit->applicationable->characters
                ? $recruit->applicationable->characters->pluck('character_id')
                : $recruit->applicationable->character_id
            )
            ->flatten()
            ->toArray();
    }
}
