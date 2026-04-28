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

namespace Seatplus\Web\Http\Controllers\AccessControl;

use Illuminate\Http\RedirectResponse;
use Seatplus\Auth\Enums\RoleType;
use Seatplus\Auth\Models\User;
use Seatplus\Auth\Services\Roles\BaseRoleService;

class LeaveControlGroupController
{
    private const ERROR_INVALID_GROUP_TYPE = 'This action is not allowed on this access control group';

    private const ERROR_UNAUTHORIZED = 'You are not allowed to perform this action';

    private const ALLOWED_ROLE_TYPES = [RoleType::OPT_IN, RoleType::ON_REQUEST];

    public function __construct(
        private BaseRoleService $roleService
    ) {}

    public function __invoke(int $role_id, int $user_id): RedirectResponse
    {
        $user = User::query()->findOrFail($user_id);
        $this->roleService = $this->roleService->for($role_id);

        $this->validateRequest($user);

        $this->processLeaveRequest($user);

        session()->flash('success');

        return redirect()->back();
    }

    private function validateRequest(User $user): void
    {
        abort_unless(in_array($this->roleService->getType(), self::ALLOWED_ROLE_TYPES), 403, self::ERROR_INVALID_GROUP_TYPE);

        abort_unless($this->isAuthorized($user), 403, self::ERROR_UNAUTHORIZED);
    }

    private function isAuthorized(User $user): bool
    {
        $authenticatedUser = auth()->user();

        return $authenticatedUser->id === $user->id
            || $authenticatedUser->can('superuser')
            || $this->roleService->canModerate($authenticatedUser);
    }

    private function processLeaveRequest(User $user): void
    {
        $roleType = $this->roleService->getType();
        match ($roleType) {
            RoleType::OPT_IN => $this->roleService->optIn()->leaveRole($user),
            RoleType::ON_REQUEST => $this->roleService->onRequest()->removeApplication($user),
            default => throw new \InvalidArgumentException(self::ERROR_INVALID_GROUP_TYPE)
        };

        $this->roleService->handleMembers();
    }
}
