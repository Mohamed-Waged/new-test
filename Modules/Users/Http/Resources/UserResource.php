<?php

namespace Modules\Users\Http\Resources;

use Helper;
use App\Models\Imageable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @param array $request
     * @return array
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function toArray($request): array
    {
        // image
        $imageUrl   = $this->image['url'] ?? NULL;
        $image      = Imageable::getImagePath('users', $imageUrl);

        return [
            'id'            => $this->id,
            'encryptId'     => encrypt($this->id),
            'image'         => $image,

            'name'          => $this->name,
            'email'         => $this->email,
            'countryCode'   => $this->country_code,
            'mobile'        => $this->mobile,

            'role'  => [
                'id'        => $this->roles()->first()->id ?? NULL,
                'name'      => $this->roles()->first()->name ?? NULL
            ],

            'status'        => Helper::getStatusKey($this->is_active),
            'date'          => date('d-m-Y', strtotime($this->created_at))
        ];
    }
}
