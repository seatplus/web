<?php


namespace Seatplus\Web\Http\Controllers\Request;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateOpenRecruitmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {

        return $this->user()->can('can open or close corporations for recruitment');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            'corporation_id' => ['required', 'exists:corporation_infos,corporation_id'],
            'type' => ['required', Rule::in(['character','user'])],
        ];
    }

}
