<?php

declare(strict_types=1);

namespace Seatplus\Web\Http\Controllers\AccessControl;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Seatplus\Auth\Enums\RoleType;
use Seatplus\Auth\Services\Roles\BaseRoleService;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Controllers\Request\ManageRoleRequest;

class UpdateManualGroupController extends Controller
{
    public function __construct(
        private readonly BaseRoleService $baseRoleService,
    ) {}

    public function __invoke(ManageRoleRequest $request, int $role_id): RedirectResponse
    {
        $validated = $request->validated();
        $roleService = $this->baseRoleService->for($role_id)->manual();

        $roleService->setRoleType(RoleType::MANUAL);

        if ($name = Arr::get($validated, 'name')) {
            $roleService->updateRoleName($name);
        }

        $roleService->handleMembers();

        return redirect()->route('acl.detail', $role_id)->with('success', 'updated');
    }
}
