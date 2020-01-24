<?php


namespace Seatplus\Web\Http\Controllers\Request;


use Illuminate\Foundation\Http\FormRequest;

class UpdateControlGroup extends FormRequest
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
            'permissions.*.name' => 'string',
            'allowed.*.character_id' => 'integer',
            'allowed.*.corporation_id' => 'integer',
            'allowed.*.alliance_id' => 'integer',
            'inverse.*.character_id' => 'integer',
            'inverse.*.corporation_id' => 'integer',
            'inverse.*.alliance_id' => 'integer',
            'forbidden.*.character_id' => 'integer',
            'forbidden.*.corporation_id' => 'integer',
            'forbidden.*.alliance_id' => 'integer',
            'roleName' => 'required|string'
        ];
    }

}
