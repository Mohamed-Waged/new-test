<?php

namespace Modules\Messages\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class StoreRequest extends FormRequest
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
            'name'          => 'required|string',
            'email'         => 'required|email',
            //'countryCode'   => 'required',
            //'mobile'        => 'required',
            //'subject'       => 'required',
            'question'      => 'required',
            'answer'        => 'nullable'
        ];
    }
}