<?php


namespace Seatplus\Web\Http\Controllers\Request;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DispatchIndividualJob extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return !auth()->guest();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $jobs = array_keys(config('eveapi.jobs'));
        $character_ids = auth()->user()->characters->pluck('character_id')->toArray();

        return [
            'job' => ['required', Rule::in($jobs)],
            'character_id' => ['required', Rule::in($character_ids)]
        ];
    }

}
