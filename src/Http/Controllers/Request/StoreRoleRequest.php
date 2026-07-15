<?php

declare(strict_types=1);

namespace Seatplus\Web\Http\Controllers\Request;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Seatplus\Auth\Enums\AffiliationType;
use Seatplus\Auth\Enums\RoleType;
use Seatplus\Web\Support\AccessControl\AssignablePermissions;

/**
 * The create-group wizard's single submit: name + join method (type) + the "Applies to"
 * affiliations + eligibility criteria + granted permissions, persisted in one request by
 * StoreControlGroupController.
 */
class StoreRoleRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'type' => ['required', Rule::in(array_map(fn (RoleType $type) => $type->value, RoleType::cases()))],
            'affiliated' => 'nullable|array',
            'affiliated.*.entity_id' => 'required|integer',
            'affiliated.*.entity_type' => ['required', 'string', Rule::in(['character', 'corporation', 'alliance'])],
            'affiliated.*.affiliation_type' => ['required', 'string', Rule::in(array_map(fn (AffiliationType $type) => $type->value, AffiliationType::cases()))],
            'assigned' => 'nullable|array',
            'assigned.*.entity_id' => 'required|integer',
            'assigned.*.entity_type' => ['required', 'string', Rule::in(['character', 'corporation', 'alliance'])],
            'permissions' => 'nullable|array',
            'permissions.*' => ['string', Rule::in(AssignablePermissions::all()->all())],
        ];
    }
}
