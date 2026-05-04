<?php

declare(strict_types=1);

namespace Seatplus\Web\Http\Controllers\AccessControl;

use Illuminate\Http\RedirectResponse;
use Seatplus\Auth\Models\User;
use Seatplus\Auth\Services\Roles\BaseRoleService;
use Seatplus\Web\Http\Controllers\Controller;

class RemoveManualMemberController extends Controller
{
    public function __construct(
        private readonly BaseRoleService $baseRoleService,
    ) {}

    public function __invoke(int $role_id, int $user_id): RedirectResponse
    {
        $service = $this->baseRoleService->for($role_id)->manual();

        $user = auth()->user();
        abort_unless(
            $user->can('administrate access control groups') || $service->canModerate($user),
            403
        );

        $service->removeMember(User::findOrFail($user_id));
        $this->baseRoleService->handleMembers();

        return redirect()->back();
    }
}
