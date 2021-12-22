<?php

namespace Seatplus\Web\Http\Controllers\Request;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class UpdateWatchlistRequest extends FormRequest
{
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
        return [
            'systems' => ['array'],
            'regions' => ['array'],
            'items' => [
                'array',
                function($attribute, $items_array, $fail) {
                    foreach ($items_array as $item) {
                        if(! Arr::has($item, ['watchable_id', 'watchable_type'])) {
                            $fail('The '.$attribute.' is invalid. Missing watchable_id and/or watchable_type');
                        }
                    }
                }
            ],
        ];
    }
}