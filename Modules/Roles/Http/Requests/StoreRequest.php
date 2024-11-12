<?php

namespace Modules\Roles\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class StoreRequest extends FormRequest
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
            'role'              => 'required|alpha_dash|max:25|unique:roles,name',
            'rolePermissions'   => 'nullable|array'
        ];
    }
}
