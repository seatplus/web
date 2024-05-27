<?php

namespace Seatplus\Web\Http\Controllers\Request;



use Illuminate\Foundation\Http\FormRequest;

class GetAssetLocationsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'character_ids' => ['required', 'array'],
            'search' => 'string',
            // only unknown is a boolean if it is set
            'only_unknown_locations' => 'filled',
            'systems' => 'array',
            'regions' => 'array',
            'types' => 'array',
            'groups' => 'array',
            'categories' => 'array',
            'withUnknownLocations' => 'missing'
        ];
    }

}
