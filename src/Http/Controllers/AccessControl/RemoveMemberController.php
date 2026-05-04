<?php

declare(strict_types=1);

namespace Seatplus\Web\Http\Controllers\AccessControl;

use Illuminate\Http\RedirectResponse;
use Seatplus\Auth\Enums\RoleType;
use Seatplus\Auth\Models\User;
use Seatplus\Auth\Services\Roles\BaseRoleService;
use Seatplus\Web\Http\Controllers\Controller;

class RemoveMemberController extends Controller
{
    public function __construct(
        private readonly BaseRoleService $baseRoleService,
    ) {}

    public function __invoke(int $role_id, int $user_id): RedirectResponse
    {
        $roleService = $this->baseRoleService->for($role_id);

        $actor = auth()->user();
        abort_unless(
            $actor->can('administrate access control groups') || $roleService->canModerate($actor),
            403
        );

        abort_unless(
            $roleService->getType() !== RoleType::AUTOMATIC,
            422,
            'Cannot remove members from automatic roles'
        );

        $user = User::findOrFail($user_id);

        match ($roleService->getType()) {
            RoleType::MANUAL => $roleService->manual()->removeMember($user),
            RoleType::ON_REQUEST => $roleService->onRequest()->removeApplication($user),
            RoleType::OPT_IN => $roleService->optIn()->leaveRole($user),
        };

        $roleService->handleMembers();

        return redirect()->back();
    }
}
