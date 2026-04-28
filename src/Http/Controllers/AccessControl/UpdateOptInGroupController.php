<?php

/*
 * MIT License
 *
 * Copyright (c) 2019, 2020, 2021 Felix Huber
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

declare(strict_types=1);

namespace Seatplus\Web\Http\Controllers\AccessControl;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Seatplus\Auth\Enums\RoleType;
use Seatplus\Auth\Services\Roles\BaseRoleService;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Controllers\Request\ManageRoleRequest;

class UpdateOptInGroupController extends Controller
{
    public function __construct(
        private readonly BaseRoleService $baseRoleService,
    ) {}

    public function __invoke(ManageRoleRequest $request, int $role_id): RedirectResponse
    {
        $validated = $request->validated();
        $roleService = $this->baseRoleService->for($role_id)->optIn();

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
            $roleService->addCriteriaForRole(
                collect($assigned)
                    ->map(fn (array $entity) => [$entity['entity_id'], $entity['entity_type']])
                    ->all()
            );
        }

        $roleService->setRoleType(RoleType::OPT_IN);

        return redirect()->route('acl.detail', $role_id)->with('success', 'updated');
    }
}
