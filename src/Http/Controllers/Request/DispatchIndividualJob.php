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

namespace Seatplus\Web\Http\Controllers\Request;

use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Seatplus\Web\Contracts\WebJobsRepository;
use Seatplus\Web\Services\GetAffiliatedIds;

class DispatchIndividualJob extends FormRequest
{
    private const string PERMISSIONS_CONFIG_KEY = 'eveapi.permissions';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return ! auth()->guest();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            ...$this->getDispatchObjectRules(),
            ...$this->getIdentificationRules(),
        ];
    }

    private function getDispatchObjectRules(): array
    {
        $availableJobs = app(WebJobsRepository::class)->getJobKeys();

        return [
            'dispatch_transfer_object.manual_job' => ['required', Rule::in($availableJobs)],
            'dispatch_transfer_object.permission' => ['required', Rule::in(config(self::PERMISSIONS_CONFIG_KEY))],
            'dispatch_transfer_object.required_corporation_role' => ['present'],
        ];
    }

    /**
     * One submitted id, one bounded probe. `coversCharacter()` / `coversCorporation()` each resolve to a
     * single AffiliationResolver::coveredIds() call against just that id — an index-driven predicate —
     * where Rule::in() used to need the user's entire affiliated set materialised in PHP first (for an
     * inverse or alliance-wide role, effectively a whole *_infos table) to check one value.
     *
     * Closure rules match Rule::in()'s presence semantics: ClosureValidationRule is not an ImplicitRule,
     * so an absent character_id/corporation_id is skipped rather than rejected — which is what makes the
     * either/or pairing with requiredIf() work.
     */
    private function getIdentificationRules(): array
    {
        $dispatchData = $this->get('dispatch_transfer_object');
        $permission = data_get($dispatchData, 'permission') ?? '';
        $corporationRoles = data_get($dispatchData, 'required_corporation_role') ?? '';
        $getAffiliatedIds = new GetAffiliatedIds;

        return [
            'character_id' => [
                Rule::requiredIf(fn () => ! $this->get('corporation_id')),
                function (string $attribute, mixed $value, Closure $fail) use ($getAffiliatedIds, $permission): void {
                    if (! $getAffiliatedIds->coversCharacter((int) $value, $permission)) {
                        $fail('The selected character is invalid.');
                    }
                },
            ],
            'corporation_id' => [
                Rule::requiredIf(fn () => ! $this->get('character_id')),
                function (string $attribute, mixed $value, Closure $fail) use ($getAffiliatedIds, $permission, $corporationRoles): void {
                    if (! $getAffiliatedIds->coversCorporation((int) $value, $permission, $corporationRoles)) {
                        $fail('The selected corporation is invalid.');
                    }
                },
            ],
        ];
    }
}
