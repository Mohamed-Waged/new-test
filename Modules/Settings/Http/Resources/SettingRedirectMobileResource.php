<?php

namespace Modules\Settings\Http\Resources;

use Helper;
use App\Models\Imageable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingRedirectMobileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        // image
        $imageUrl   = $this->image['url'] ?? NULL;
        $image      = Imageable::getImagePath('settings', $imageUrl, true);

        return [
            'id'            => $this->id,
            'media' => [
                'type'      => Helper::getFileExtensionType($image),
                'url'       => $image
            ],
            'title'         => $this->title,
            'body'          => $this->body,
            'module'        => $this->moduleable_type,
            'moduleId'      => $this->moduleable_id
        ];
    }
}
