<?php

namespace App\Http\Resources\Auth;

use App\Models\Imageable;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthTokenResource extends JsonResource
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
            'image'         => $image,
            'name'          => $this->name,
            'email'         => $this->email,
            'countryCode'   => $this->country_code,
            'mobile'        => $this->mobile,
            'role'          => auth('api')->user()->roles()->first()->name,
            'locale'        => $this->locale,
            'tokenType'     => 'bearer',
            'accessToken'   => $this->token,
            'expiresIn'     => auth('api')->factory()->getTTL() * 60
        ];
    }
}
