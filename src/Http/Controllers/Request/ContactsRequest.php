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
use Illuminate\Validation\Validator;
use Seatplus\Eveapi\Models\Character\CharacterAffiliation;
use Seatplus\Eveapi\Models\Contacts\Contact;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Web\Services\GetRecruitIdsService;

class ContactsRequest extends FormRequest
{
    private CharacterAffiliation $affiliation;
    private array $affiliated_ids;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $character_id = request()->route()->parameter('character_id');

        $this->affiliation = CharacterAffiliation::firstWhere('character_id', $character_id);

        return [
            'corporation_id' => [
                'required',
                'integer',
            ],
            'alliance_id' => [
                'sometimes',
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    if ($value !== CorporationInfo::find($this->get('corporation_id'))->alliance_id) {
                        $fail("The provided ${attribute} does not match the corporations ${attribute}");
                    }
                }, ],
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function (Validator $validator) {
            if (! $this->has('corporation_id')) {
                $this->merge([
                    'corporation_id' => $this->affiliation->corporation_id,
                    'alliance_id' => $this->affiliation->alliance_id,
                ]);
            }
        });
    }

    private function getAffiliatedIds()
    {
        if (! isset($this->affiliated_ids)) {
            $this->affiliated_ids = [...getAffiliatedIdsByClass(Contact::class), ...GetRecruitIdsService::get()];
        }

        return $this->affiliated_ids;
    }
}
