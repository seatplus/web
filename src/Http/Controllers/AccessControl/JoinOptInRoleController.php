<?php

declare(strict_types=1);

namespace Seatplus\Web\Http\Controllers\AccessControl;

use Illuminate\Http\RedirectResponse;
use Seatplus\Auth\Enums\RoleType;
use Seatplus\Auth\Http\Actions\Roles\OptIn\JoinAction;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
use Seatplus\Auth\Services\Roles\BaseRoleService;

class JoinOptInRoleController
{
    public function __construct(
        private readonly BaseRoleService $baseRoleService,
    ) {}

    public function __invoke(int $role_id): RedirectResponse
    {
        $role = Role::findOrFail($role_id);
        abort_unless($role->type === RoleType::OPT_IN, 403, 'Only opt-in roles can be joined this way');

        /** @var User $user */
        $user = auth()->user();

        app(JoinAction::class)->execute($role_id, $user->id);

        $this->baseRoleService->for($role_id)->handleMembers();

        return redirect()->back();
    }
}
