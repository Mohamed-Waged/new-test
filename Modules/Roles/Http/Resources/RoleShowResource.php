<?php

namespace Modules\Roles\Http\Resources;

use Illuminate\Http\Request;
use Modules\Roles\Entities\Permission;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @param $request
     * @return array
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function toArray($request): array
    {   
        return [
            
            'id'                => $this->id,
            'encryptId'         => encrypt($this->id),
            'title'             => $this->name,

            'details'  => [
                'name'          => $this->name,
                'permissions'   => Permission::getPermissions($this->id)
            ]
        ];
    }
}