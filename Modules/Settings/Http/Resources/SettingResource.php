<?php

namespace Modules\Settings\Http\Resources;

use Helper;
use App\Models\Imageable;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
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

        // user image
        $usrImageUrl   = $this->user->image['url'] ?? NULL;
        $usrImage      = Imageable::getImagePath('employees', $usrImageUrl, true);

        return [
            'id'                => $this->id,
            'encryptId'         => encrypt($this->id),
            'parentId'          => encrypt($this->parent_id),
            'image'             => $image,
            'media' => [
                'type'          => Helper::getFileExtensionType($image),
                'url'           => $image,
            ],


            // user
            'user' => [
                'image'         => $usrImage,
                'name'          => $this->user ? $this->user->name : '',
            ],

            'slug'              => $this->slug,
            'icon'              => $this->icon,

            //
            'moduleable' => [
                'id'            => $this->moduleable_id,
                //'name'          => Helper::getModuleableTitle($this->moduleable_id, $this->moduleable_type),
            ],
            'moduleableType'    => $this->moduleable_type,

            //
            'sort'              => (int)$this->sort,
            'body'              => $this->value,
            'status'            => $this->is_active ? 'Active' : 'Inactive',
            'date'              => date('d-m-Y', strtotime($this->created_at)),

            // translations
            'en' => [
                'title'         => $this->translate('en')['title'],
                'body'          => $this->translate('en')['body']
            ],
            'ar' => [
                'title'         => $this->translate('ar')['title'],
                'body'          => $this->translate('ar')['body']
            ]
        ];
    }
}
