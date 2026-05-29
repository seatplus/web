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

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
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
        $dispatchData = $this->get('dispatch_transfer_object');
        $affiliatedIds = $this->getAffiliatedIdsForDispatch($dispatchData);

        return [
            ...$this->getDispatchObjectRules(),
            ...$this->getIdentificationRules($affiliatedIds),
        ];
    }

    private function getAffiliatedIdsForDispatch(array $dispatchData): array
    {
        return (new GetAffiliatedIds)->get(
            permissions: data_get($dispatchData, 'permission'),
            corporationRoles: data_get($dispatchData, 'required_corporation_role') ?? '',
        );
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

    private function getIdentificationRules(array $affiliatedIds): array
    {
        return [
            'character_id' => [
                Rule::requiredIf(fn () => ! $this->get('corporation_id')),
                Rule::in($this->getAffiliatedCharacterIds($affiliatedIds)),
            ],
            'corporation_id' => [
                Rule::requiredIf(fn () => ! $this->get('character_id')),
                Rule::in($this->getAffiliatedCorporationIds($affiliatedIds)),
            ],
        ];
    }

    private function getAffiliatedCharacterIds(array $affiliatedIds): array
    {

        return CharacterInfo::query()
            ->whereIn('character_id', $affiliatedIds)
            ->pluck('character_id')
            ->values()
            ->toArray();
    }

    private function getAffiliatedCorporationIds(array $affiliatedIds): array
    {

        return CorporationInfo::query()
            ->whereIn('corporation_id', $affiliatedIds)
            ->pluck('corporation_id')
            ->values()
            ->toArray();
    }
}
