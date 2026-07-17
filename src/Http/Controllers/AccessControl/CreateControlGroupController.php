<?php

declare(strict_types=1);

namespace Seatplus\Web\Http\Controllers\AccessControl;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Seatplus\Auth\Http\Actions\Roles\ManageAutomaticRoleAction;
use Seatplus\Auth\Http\Actions\Roles\Manual\ManageManualRoleAction;
use Seatplus\Auth\Http\Actions\Roles\OnRequest\ManageOnRequestRoleAction;
use Seatplus\Auth\Http\Actions\Roles\OptIn\ManageOptInRoleAction;
use Seatplus\Auth\Http\Requests\RoleRequest;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Controllers\Request\StoreRoleRequest;
use Seatplus\Web\Support\AccessControl\AssignablePermissions;
use Seatplus\Web\Support\AccessControl\RoleTypeMetadata;
use Seatplus\Web\Support\Translations;

class CreateControlGroupController extends Controller
{
    private const array TYPE_ACTION_MAP = [
        'automatic' => ManageAutomaticRoleAction::class,
        'manual' => ManageManualRoleAction::class,
        'on-request' => ManageOnRequestRoleAction::class,
        'opt-in' => ManageOptInRoleAction::class,
    ];

    /** Render the guided create wizard. */
    public function create(): Response
    {
        return Inertia::render('AccessControl/CreateRole', [
            'joinMethods' => RoleTypeMetadata::all(),
            'availablePermissions' => AssignablePermissions::all(),
            'activeSidebarElement' => 'acl.hub',
            'pageTranslations' => Translations::gather(['web::access_control']),
        ]);
    }

    /** Create + fully configure a group in one transaction (the wizard's single submit). */
    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $role = DB::transaction(function () use ($request, $validated): Role {
            $role = Role::query()->create(['name' => $validated['name']]);

            // Reuse the exact per-type action by handing it a validated RoleRequest carrying the
            // new role_id — same code path as the configure page, so create/edit never diverge.
            $roleRequest = RoleRequest::createFrom($request);
            $roleRequest->setContainer(app());
            $roleRequest->merge(['role_id' => $role->id]);
            $roleRequest->validateResolved();

            app(self::TYPE_ACTION_MAP[$validated['type']])->execute($roleRequest);

            // findOrCreate so config-declared permissions that were never persisted still resolve.
            $role->syncPermissions(
                collect($validated['permissions'] ?? [])->map(fn (string $name) => Permission::findOrCreate($name))->all()
            );

            return $role;
        });

        return redirect()->route('acl.hub.show', $role->id)->with('success', 'Group created');
    }
}
