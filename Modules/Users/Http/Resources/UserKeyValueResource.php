<?php

namespace Modules\Users\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserKeyValueResource extends JsonResource
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
            'name'      => $this->name,
        ];
    }
}