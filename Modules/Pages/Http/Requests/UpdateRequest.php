<?php

namespace Modules\Pages\Http\Requests;

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
            'imageBase64'   => 'nullable',
            
            'en.title'      => [
                                'required', 
                                    Rule::unique('page_translations','title')
                                            ->where('locale', 'en')
                                            ->ignore(decrypt($id), 'page_id')
                            ],
            'en.title'      => [
                                'required', 
                                    Rule::unique('page_translations','title')
                                            ->where('locale', 'ar')
                                            ->ignore(decrypt($id), 'page_id')
                            ],

            'en.body'       => 'nullable',
            'ar.body'       => 'nullable',

            'sort'          => 'required|numeric',
            'status'        => 'required|in:active,inactive'
        ];
    }
}
