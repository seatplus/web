<?php

/*
 * MIT License
 *
 * Copyright (c) 2019, 2020 Felix Huber
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
use Seatplus\Eveapi\Jobs\Hydrate\Character\HydrateCharacterBase;
use Seatplus\Eveapi\Jobs\ManualDispatchableJobInterface;

class DispatchIndividualJob extends FormRequest
{
    protected array $dispatch_transfer_object = [];

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

        $affiliated_ids = $this->buildAffiliatedIds();

        return [
            'dispatch_transfer_object.manual_job' => ['required', Rule::in($jobs)],
            'dispatch_transfer_object.permission' => ['required', Rule::in(config('eveapi.permissions'))],
            'dispatch_transfer_object.required_corporation_role' => ['present'],
            'character_id' => [Rule::requiredIf(fn () => ! $this->get('corporation_id')), Rule::in($affiliated_ids)],
            'corporation_id' => [Rule::requiredIf(fn () => ! $this->get('character_id')), Rule::in($affiliated_ids)],
        ];
    }

    private function buildAffiliatedIds(): array
    {
        return getAffiliatedIdsByPermission(Arr::get($this->dispatch_transfer_object, 'permission'));
    }

   /* private function buildDispatchableJob(): HydrateCharacterBase
    {
        $dispatchable_job_class = Arr::get(config('web.jobs'), Arr::get($this->dispatch_transfer_object, 'manual_job'));

        if (! $dispatchable_job_class) {
            throw new \Exception('unable to get lock of the dispatchable job');
        }

        return new $dispatchable_job_class;
    }*/
}
