<?php

namespace Modules\Coupons\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CouponKeyValueResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @param array $request
     * @return array
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function toArray($request): array
    {
        return [
            'id'        => $this->id,
            'name'      => $this->title,
        ];
    }
}
