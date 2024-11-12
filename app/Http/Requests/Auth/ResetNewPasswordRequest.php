<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ResetNewPasswordRequest extends FormRequest
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
            'email'                     => 'required|email|exists:users,email',
            'validation_code'           => 'required|min:4|max:4',
            'new_password'              => 'required|string|min:6|max:50|same:new_password_confirmation',
            'new_password_confirmation' => 'required|same:new_password'
        ];
    }
}
