<?php

declare(strict_types=1);

namespace Seatplus\Web\Http\Controllers\AccessControl;

use Illuminate\Http\RedirectResponse;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
use Seatplus\Auth\Services\Roles\AbstractRoleService;
use Seatplus\Auth\Services\Roles\BaseRoleService;
use Seatplus\Auth\Services\Roles\ManualRoleService;
use Seatplus\Auth\Services\Roles\OnRequestRoleService;
use Seatplus\Web\Http\Controllers\Controller;

class SetModeratorController extends Controller
{
    public function __construct(
        private readonly BaseRoleService $baseRoleService,
    ) {}

    public function add(int $role_id, int $user_id): RedirectResponse
    {
        return $this->set($role_id, $user_id, true);
    }

    public function remove(int $role_id, int $user_id): RedirectResponse
    {
        return $this->set($role_id, $user_id, false);
    }

    private function set(int $role_id, int $user_id, bool $can_moderate): RedirectResponse
    {
        $this->baseRoleService->for(Role::findOrFail($role_id));

        /** @var OnRequestRoleService|ManualRoleService $roleService */
        $roleService = $this->baseRoleService->getTypeService();

        abort_unless(
            $roleService instanceof OnRequestRoleService || $roleService instanceof ManualRoleService,
            403,
            'Moderators can only be set on manual or on-request roles'
        );

        $roleService->setModerator(User::findOrFail($user_id), $can_moderate);

        return redirect()->back()->with('success', $can_moderate ? 'Moderator added' : 'Moderator removed');
    }
}
