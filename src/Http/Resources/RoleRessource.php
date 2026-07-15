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

namespace Seatplus\Web\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Seatplus\Auth\Enums\RoleType;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
use Seatplus\Auth\Services\Roles\BaseRoleService;
use Seatplus\Web\Support\AccessControl\RoleTypeMetadata;

/**
 * @mixin Role
 */
class RoleRessource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $user = auth()->user();
        $service = (new BaseRoleService)->for($this->resource);
        $meta = RoleTypeMetadata::for($this->type);

        $isSuperuser = $user->can('superuser');
        // Match the route middleware ('administrate access control groups'), not the old
        // mismatched 'create,update and delete access control group' permission string.
        $canEdit = $isSuperuser || $user->can('administrate access control groups');

        $myStatus = $this->myStatus($user);          // 'active' | 'pending' | false
        // canJoin() == meetsCriteria() for on-request/opt-in (eligible to apply/join).
        $isEligible = $service->canJoin($user);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type->value,
            'type_label' => $meta['label'],
            'type_description' => $meta['description'],
            'members' => $this->users->count(),
            'my_status' => $myStatus,
            'can_edit' => $canEdit,
            // Fixed: delegate to the service for ALL moderated types (manual/on-request/opt-in),
            // not the old hardcoded ON_REQUEST-only check. Automatic roles can't be moderated.
            'can_moderate' => $isSuperuser || $service->canModerate($user),
            // Per-user, per-type action affordances (apply = on-request, join = opt-in).
            'can_apply' => $this->type === RoleType::ON_REQUEST && $myStatus === false && $isEligible,
            'can_join' => $this->type === RoleType::OPT_IN && $myStatus === false && $isEligible,
            'can_leave' => $myStatus !== false && $this->type !== RoleType::AUTOMATIC,
        ];
    }

    /**
     * The authenticated user's membership status for this role, or false if not a member.
     */
    private function myStatus(User $user): string|false
    {
        return $this->roleMemberships()
            ->where('entity_type', User::class)
            ->where('entity_id', $user->getAuthIdentifier())
            ->value('status') ?? false;
    }
}
