<?php

namespace Modules\Books\Http\Requests;

use Illuminate\Validation\Rule;
use App\Constants\GlobalConstants;
use Illuminate\Foundation\Http\FormRequest;

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
            'lecturer'          => 'required|array',
            'lecturer.id'       => 'required|exists:lecturers,id',

            'book_type'         => 'required|array',
            'book_type.id'      => 'required|exists:settings,id',

            'imageBase64'       => 'nullable',
            'filesBase64'       => 'nullable',

            'en.title'          => [
                'required',
                'regex:' . GlobalConstants::TITLE_REGEX,
                Rule::unique('book_translations', 'title')
                    ->where('locale', 'en')
                    ->ignore(decrypt($id), 'book_id')
            ],

            'ar.title'          => [
                'required',
                'regex:' . GlobalConstants::TITLE_REGEX,
                Rule::unique('book_translations', 'title')
                    ->where('locale', 'ar')
                    ->ignore(decrypt($id), 'book_id')
            ],

            'en.body'           => 'nullable|regex:' . GlobalConstants::TITLE_REGEX,
            'ar.body'           => 'nullable|regex:' . GlobalConstants::TITLE_REGEX,

            'price'             => 'required|between:0,'. GlobalConstants::MAX_PRICE,
            'pages_no'          => 'required|numeric',
            'published_at'      => 'required|date',

            'sort'              => 'required|numeric',
            'status'            => 'required|in:active,inactive'
        ];
    }
}
