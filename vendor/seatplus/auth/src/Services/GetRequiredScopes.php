<?php


namespace Seatplus\Auth\Services;


use Illuminate\Support\Collection;
use Seatplus\Auth\Models\User;

class GetRequiredScopes
{
    private Collection $scopes;

    private User $user;

    public function __construct()
    {
        $this->scopes = collect(config('eveapi.scopes.minimum'));

        /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
        $this->user = User::with(
            'application.corporation.ssoScopes',
            'application.corporation.alliance.ssoScopes'
        )->find(auth()->user()->getAuthIdentifier());
    }

    public function execute() : Collection
    {
        if (auth()->guest()) {
            return $this->scopes->merge(setting('global_sso_scopes'));
        }

        return $this->scopes
            ->merge(collect([
                    setting('global_sso_scopes'),
                    $this->user->application->corporation->ssoScopes->selected_scopes ?? [],
                    $this->user->application->corporation->alliance->ssoScopes->selected_scopes ?? [],
                ])
            )
            ->flatten(1)
            ->unique()
            ->filter();
    }

}
