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
            ...$this->getIdentificationRules($this->get('dispatch_transfer_object')),
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
     * Validate the submitted character_id / corporation_id against the user's affiliated set with a
     * bounded membership probe (coversCharacter / coversCorporation) rather than materialising the whole
     * affiliated set into a Rule::in list — an inverse/alliance-wide role no longer pulls a *_infos table
     * into PHP just to validate one id.
     *
     * @param  array<string, mixed>|null  $dispatchData
     * @return array<string, array<int, mixed>>
     */
    private function getIdentificationRules(?array $dispatchData): array
    {
        $permission = data_get($dispatchData, 'permission') ?? '';
        $corporationRole = data_get($dispatchData, 'required_corporation_role') ?? '';

        return [
            'character_id' => [
                Rule::requiredIf(fn () => ! $this->get('corporation_id')),
                function (string $attribute, mixed $value, Closure $fail) use ($permission): void {
                    if (! (new GetAffiliatedIds)->coversCharacter((int) $value, $permission)) {
                        $fail('The selected character is not among your affiliated characters.');
                    }
                },
            ],
            'corporation_id' => [
                Rule::requiredIf(fn () => ! $this->get('character_id')),
                function (string $attribute, mixed $value, Closure $fail) use ($permission, $corporationRole): void {
                    if (! (new GetAffiliatedIds)->coversCorporation((int) $value, $permission, $corporationRole)) {
                        $fail('The selected corporation is not among your affiliated corporations.');
                    }
                },
            ],
        ];
    }
}
