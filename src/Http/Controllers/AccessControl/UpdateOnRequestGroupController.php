<?php

declare(strict_types=1);

namespace Seatplus\Web\Http\Controllers\AccessControl;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Seatplus\Auth\Enums\AffiliationType;
use Seatplus\Auth\Enums\RoleType;
use Seatplus\Auth\Services\Roles\BaseRoleService;
use Seatplus\Auth\Services\Roles\DTO\AffiliationData;
use Seatplus\Auth\Services\Roles\DTO\CriteriaData;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Controllers\Request\ManageRoleRequest;

class UpdateOnRequestGroupController extends Controller
{
    public function __construct(
        private readonly BaseRoleService $baseRoleService,
    ) {}

    public function __invoke(ManageRoleRequest $request, int $role_id): RedirectResponse
    {
        $validated = $request->validated();
        $roleService = $this->baseRoleService->for($role_id)->onRequest();

        $roleService->setRoleType(RoleType::ON_REQUEST);

        if ($name = Arr::get($validated, 'name')) {
            $roleService->updateRoleName($name);
        }

        if ($affiliated = Arr::get($validated, 'affiliated')) {
            $roleService->syncAffiliateManyEntities(
                ...collect($affiliated)
                    ->map(fn (array $entity) => new AffiliationData(
                        entity_id: $entity['entity_id'],
                        entity_type: $entity['entity_type'],
                        affiliation_type: AffiliationType::from($entity['affiliation_type']),
                    ))
                    ->all()
            );
        }

        if ($assigned = Arr::get($validated, 'assigned')) {
            $roleService->addCriteriaForRoleApplication(
                ...collect($assigned)
                    ->map(fn (array $entity) => new CriteriaData(
                        entity_id: $entity['entity_id'],
                        entity_type: $entity['entity_type'],
                    ))
                    ->all()
            );
        }

        $roleService->setRoleType(RoleType::ON_REQUEST);
        $roleService->handleMembers();

        return redirect()->route('acl.detail', $role_id)->with('success', 'updated');
    }
}
