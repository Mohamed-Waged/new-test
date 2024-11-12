<?php

namespace Modules\Users\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * @return bool
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function rules(): array
    {
        $id = last(request()->segments());

        return [
            'imageBase64'       => 'nullable',
            'name'              => 'required|string',
            'email'             => 'required|email|unique:users,email,' . decrypt($id),
            'password'          => 'nullable|string|min:6|max:50',
            'countryCode'       => 'required|string',
            'mobile'            => 'required',
            'role'              => 'required|array',
            'status'            => 'required|in:Active,Inactive'
        ];
    }
}