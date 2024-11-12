<?php

namespace Modules\Settings\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingKeyValueResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id'    => $this->id,
            'name'  => $this->translate(app()->getLocale())['title'],
        ];
    }
}
