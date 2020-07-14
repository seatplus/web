<?php


namespace Seatplus\Web\Http\Controllers\Request;


use Illuminate\Foundation\Http\FormRequest;

class ControlGroupUpdate extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->can('manage access control group');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            'type' => ['required', 'string'],
            'affiliations' => ['array'],
            'members' => ['array']
        ];
    }

}
