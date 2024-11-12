<?php

namespace Modules\Settings\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class UpdateRequest extends FormRequest
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
            'nursery'           => 'nullable',
            'nursery.id'        => 'nullable',

            'branch'            => 'nullable',
            'branch.id'         => 'nullable',

            'classroom'         => 'nullable',
            'classroom.id'      => 'nullable',
            
            'imageBase64'       => 'nullable',
            'parentId'          => 'nullable',

            'en.title'          => 'nullable',
            'ar.title'          => 'nullable',
            'en.body'           => 'nullable',
            'ar.body'           => 'nullable',
            
            'value'             => 'nullable',

            'moduleable'        => 'nullable',
            'moduleableType'    => 'nullable',

            'icon'              => 'nullable',
            'sort'              => 'nullable',
            'status'            => 'nullable'
        ];
    }
}