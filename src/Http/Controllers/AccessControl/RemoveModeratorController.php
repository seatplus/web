<?php

declare(strict_types=1);

namespace Seatplus\Web\Http\Controllers\AccessControl;

use Illuminate\Http\RedirectResponse;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
use Seatplus\Auth\Services\Roles\BaseRoleService;
use Seatplus\Auth\Services\Roles\ManualRoleService;
use Seatplus\Auth\Services\Roles\OnRequestRoleService;
use Seatplus\Auth\Services\Roles\OptInRoleService;
use Seatplus\Web\Http\Controllers\Controller;

class RemoveModeratorController extends Controller
{
    public function __construct(
        private readonly BaseRoleService $baseRoleService,
    ) {}

    public function __invoke(int $role_id, int $user_id): RedirectResponse
    {
        $this->baseRoleService->for(Role::findOrFail($role_id));
        $roleService = $this->baseRoleService->getTypeService();

        abort_unless(
            $roleService instanceof OnRequestRoleService || $roleService instanceof ManualRoleService || $roleService instanceof OptInRoleService,
            403,
            'This role type does not support moderators'
        );

        /** @var OnRequestRoleService|ManualRoleService|OptInRoleService $roleService */
        $roleService->setModerator(User::findOrFail($user_id), false);

        return redirect()->back()->with('success', 'Moderator removed');
    }
}
