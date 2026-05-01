<?php

declare(strict_types=1);

namespace Seatplus\Web\Http\Controllers\AccessControl;

use Illuminate\Http\RedirectResponse;
use Seatplus\Auth\Enums\RoleType;
use Seatplus\Auth\Http\Actions\Roles\OnRequest\ApplyAction;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;

class ApplyToRoleController
{
    public function __invoke(int $role_id): RedirectResponse
    {
        $role = Role::findOrFail($role_id);
        abort_unless($role->type === RoleType::ON_REQUEST, 403, 'Only on-request roles accept applications');

        /** @var User $user */
        $user = auth()->user();

        app(ApplyAction::class)->execute($role_id, $user->id);

        return redirect()->back();
    }
}
