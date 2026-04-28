<?php

declare(strict_types=1);

namespace Seatplus\Web\Http\Controllers\AccessControl;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Seatplus\Auth\Enums\RoleType;
use Seatplus\Auth\Services\Roles\BaseRoleService;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Controllers\Request\ManageRoleRequest;

class UpdateAutomaticGroupController extends Controller
{
    public function __construct(
        private readonly BaseRoleService $baseRoleService,
    ) {}

    public function __invoke(ManageRoleRequest $request, int $role_id): RedirectResponse
    {
        $validated = $request->validated();
        $roleService = $this->baseRoleService->for($role_id)->automatic();

        if ($name = Arr::get($validated, 'name')) {
            $roleService->updateRoleName($name);
        }

        if ($affiliated = Arr::get($validated, 'affiliated')) {
            $roleService->syncAffiliateManyEntities(
                collect($affiliated)
                    ->map(fn (array $entity) => [$entity['entity_id'], $entity['entity_type'], $entity['affiliation_type']])
                    ->all()
            );
        }

        if ($assigned = Arr::get($validated, 'assigned')) {
            $roleService->automaticallyAssignRoleTo(
                collect($assigned)
                    ->map(fn (array $entity) => [$entity['entity_id'], $entity['entity_type']])
                    ->all()
            );
        }

        $roleService->setRoleType(RoleType::AUTOMATIC);

        return redirect()->route('acl.detail', $role_id)->with('success', 'updated');
    }
}
