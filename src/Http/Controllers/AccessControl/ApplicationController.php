<?php

declare(strict_types=1);

namespace Seatplus\Web\Http\Controllers\AccessControl;

use Illuminate\Http\RedirectResponse;
use Seatplus\Auth\Enums\RoleType;
use Seatplus\Auth\Http\Actions\Roles\OnRequest\ApplyAction;
use Seatplus\Auth\Http\Actions\Roles\OnRequest\ApproveAction;
use Seatplus\Auth\Http\Actions\Roles\OnRequest\DenyAction;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
use Seatplus\Auth\Services\Roles\BaseRoleService;
use Seatplus\Web\Http\Controllers\Controller;

class ApplicationController extends Controller
{
    public function __construct(
        private readonly BaseRoleService $baseRoleService,
    ) {}

    public function apply(int $role_id): RedirectResponse
    {
        $role = Role::findOrFail($role_id);
        abort_unless($role->type === RoleType::ON_REQUEST, 403, 'Only on-request roles accept applications');

        /** @var User $user */
        $user = auth()->user();

        app(ApplyAction::class)->execute($role_id, $user->id);

        return redirect()->back();
    }

    public function approve(int $role_id, int $user_id): RedirectResponse
    {
        $this->authorizeModeration($role_id);

        app(ApproveAction::class)->execute($role_id, $user_id);

        $this->baseRoleService->for(Role::findOrFail($role_id))->handleMembers();

        return redirect()->back();
    }

    public function deny(int $role_id, int $user_id): RedirectResponse
    {
        $this->authorizeModeration($role_id);

        app(DenyAction::class)->execute($role_id, $user_id);

        return redirect()->back();
    }

    private function authorizeModeration(int $role_id): void
    {
        /** @var User $authenticated_user */
        $authenticated_user = auth()->user();

        $can_moderate = $authenticated_user->can('superuser')
            || $this->baseRoleService->for(Role::findOrFail($role_id))->canModerate($authenticated_user);

        abort_unless($can_moderate, 403, 'You are not allowed to moderate this role');
    }
}
