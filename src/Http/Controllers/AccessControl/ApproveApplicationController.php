<?php

declare(strict_types=1);

namespace Seatplus\Web\Http\Controllers\AccessControl;

use Illuminate\Http\RedirectResponse;
use Seatplus\Auth\Http\Actions\Roles\OnRequest\ApproveAction;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
use Seatplus\Auth\Services\Roles\BaseRoleService;

class ApproveApplicationController
{
    public function __construct(
        private readonly BaseRoleService $baseRoleService,
    ) {}

    public function __invoke(int $role_id, int $user_id): RedirectResponse
    {
        $role = Role::findOrFail($role_id);

        /** @var User $authenticated_user */
        $authenticated_user = auth()->user();

        $can_moderate = $authenticated_user->can('superuser')
            || $this->baseRoleService->for($role)->canModerate($authenticated_user);

        abort_unless($can_moderate, 403, 'You are not allowed to moderate this role');

        app(ApproveAction::class)->execute($role_id, $user_id);

        $this->baseRoleService->for($role)->handleMembers();

        return redirect()->back();
    }
}
