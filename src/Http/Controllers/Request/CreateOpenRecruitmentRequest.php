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

class CreateOpenRecruitmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * The `can open or close corporations for recruitment` permission is the sole gate. This is an
     * intentional authorization relaxation: unlike the edit/delete/watchlist routes, creating a Job
     * Posting is NOT scoped to affiliated corporations, so a permission-holder may open any corp.
     */
    public function authorize(): bool
    {
        return $this->user()->can('can open or close corporations for recruitment');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // Any corporation id is accepted (permission-gated, not affiliation-scoped). No
            // `exists:corporation_infos` — the corp may be brand new; the controller ensures its
            // CorporationInfo gets populated via the public-ESI id-resolution path.
            'corporation_id' => ['required', 'integer'],
            'type' => ['required', 'string', Rule::in(['character', 'user'])],
            'steps' => ['nullable', 'string'],
        ];
    }

    /*protected function prepareForValidation()
    {
        $this->merge(['corporation' => data_get($this->corporation, 'corporation_id')]);
    }*/
}
