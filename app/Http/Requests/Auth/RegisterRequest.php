<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
        return [
            'name'          => 'required|string',
            'email'         => 'required|email|unique:users',
            'country_code'  => 'nullable',
            'mobile'        => 'nullable',
            'password'      => 'required|string|min:4|max:50',
            'role'          => 'nullable|exists:roles,name',
            'fcm_token'     => 'nullable'
        ];
    }
}
