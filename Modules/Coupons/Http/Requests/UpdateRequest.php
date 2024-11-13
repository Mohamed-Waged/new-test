<?php

namespace Modules\Coupons\Http\Requests;

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
            'imageBase64'       => 'nullable',

            'lecturer'          => 'required|array',
            'lecturer.id'       => 'required|exists:lecturers,id',

            'en.title'          => [
                'required',
                'regex:' . GlobalConstants::TITLE_REGEX,
                Rule::unique('couponable_translations', 'title')
                    ->where('locale', 'en')
                    ->ignore(decrypt($id), 'couponable_id')
            ],

            'ar.title'          => [
                'required',
                'regex:' . GlobalConstants::TITLE_REGEX,
                Rule::unique('couponable_translations', 'title')
                    ->where('locale', 'ar')
                    ->ignore(decrypt($id), 'couponable_id')
            ],

            'en.body'           => 'nullable|regex:' . GlobalConstants::TITLE_REGEX,
            'ar.body'           => 'nullable|regex:' . GlobalConstants::TITLE_REGEX,

            'couponeableId'     => 'nullable',
            'couponeableType'   => 'nullable',

            'couponCount'       => 'required|numeric',
            'couponPercentage'  => 'required|numeric',

            'sort'              => 'required|numeric',
            'status'            => 'required|in:active,inactive'
        ];
    }
}