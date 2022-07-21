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
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Seatplus\Auth\Services\Dtos\AffiliationsDto;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;

class DispatchIndividualJob extends FormRequest
{
    protected array $dispatch_transfer_object = [];
    protected AffiliationsDto $affiliationsDto;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return ! auth()->guest();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $this->dispatch_transfer_object = $this->get('dispatch_transfer_object');

        $jobs = array_keys(config('web.jobs'));

        return [
            'dispatch_transfer_object.manual_job' => ['required', Rule::in($jobs)],
            'dispatch_transfer_object.permission' => ['required', Rule::in(config('eveapi.permissions'))],
            'dispatch_transfer_object.required_corporation_role' => ['present'],
            'character_id' => [Rule::requiredIf(fn () => ! $this->get('corporation_id')), Rule::in($this->getAffiliatedCharacterIds())],
            'corporation_id' => [Rule::requiredIf(fn () => ! $this->get('character_id')), Rule::in($this->getAffiliatedCorporationIds())],
        ];
    }

    public function getAffiliationsDto(): AffiliationsDto
    {
        if (! isset($this->affiliationsDto)) {
            $this->affiliationsDto = new AffiliationsDto(
                permissions: [Arr::get($this->dispatch_transfer_object, 'permission')],
                user: auth()->user(),
                corporation_roles: Arr::get($this->dispatch_transfer_object, 'required_corporation_role') ? [Arr::get($this->dispatch_transfer_object, 'required_corporation_role')] : null,
            );
        }

        return $this->affiliationsDto;
    }

    private function getAffiliatedCharacterIds(): array
    {
        $affiliationsDto = $this->getAffiliationsDto();

        return CharacterInfo::query()
            ->whereAffiliatedCharacters($affiliationsDto)
            ->pluck('character_id')
            ->values()
            ->toArray();
    }

    private function getAffiliatedCorporationIds(): array
    {
        $affiliationsDto = $this->getAffiliationsDto();

        return CorporationInfo::query()
            ->whereAffiliatedCorporations($affiliationsDto)
            ->pluck('corporation_id')
            ->values()
            ->toArray();
    }
}
