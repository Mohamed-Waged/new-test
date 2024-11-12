<?php

namespace Modules\Roles\Http\Resources;

use Illuminate\Http\Request;
use Modules\Roles\Entities\Permission;
use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
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
            'name'      => $this->resource,
            'read'      => Permission::hasPermissionAccess($this->resource, false, 'read'),
            'create'    => Permission::hasPermissionAccess($this->resource, false, 'create'),
            'update'    => Permission::hasPermissionAccess($this->resource, false, 'update'),
            'delete'    => Permission::hasPermissionAccess($this->resource, false, 'delete')
        ];
    }
}