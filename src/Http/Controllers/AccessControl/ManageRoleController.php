<?php

declare(strict_types=1);

namespace Seatplus\Web\Http\Controllers\AccessControl;

use Illuminate\Http\RedirectResponse;
use Seatplus\Auth\Http\Actions\Roles\ManageAutomaticRoleAction;
use Seatplus\Auth\Http\Actions\Roles\Manual\ManageManualRoleAction;
use Seatplus\Auth\Http\Actions\Roles\OnRequest\ManageOnRequestRoleAction;
use Seatplus\Auth\Http\Actions\Roles\OptIn\ManageOptInRoleAction;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Controllers\Request\ManageRoleRequest;

class ManageRoleController extends Controller
{
    private const array TYPE_ACTION_MAP = [
        'automatic' => ManageAutomaticRoleAction::class,
        'manual' => ManageManualRoleAction::class,
        'on-request' => ManageOnRequestRoleAction::class,
        'opt-in' => ManageOptInRoleAction::class,
    ];

    public function __invoke(ManageRoleRequest $request, int $role_id): RedirectResponse
    {
        $type = $request->route('type');

        abort_unless(array_key_exists($type, self::TYPE_ACTION_MAP), 404);

        app(self::TYPE_ACTION_MAP[$type])->execute($request);

        // Sync the permissions the group grants (only when the form submitted them, so a
        // configure save that omits the field doesn't silently revoke everything).
        if ($request->has('permissions')) {
            // findOrCreate so config-declared permissions that were never persisted still resolve.
            $permissions = collect($request->input('permissions', []))
                ->map(fn (string $name) => Permission::findOrCreate($name))
                ->all();

            Role::findById($role_id)->syncPermissions($permissions);
        }

        return redirect()->route('acl.hub.show', $role_id)->with('success', 'updated');
    }
}
