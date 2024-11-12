<?php

namespace Modules\Roles\Repositories;

use App\Repositories\BaseRepository;
use Modules\Roles\Entities\Permission;
use Modules\Roles\Http\Resources\PermissionResource;
use Modules\Roles\Repositories\Contracts\PermissionsRepositoryInterface;

class PermissionsRepository extends BaseRepository implements PermissionsRepositoryInterface
{
    protected $model;

    /**
     * @param Permission $model
     * @return void
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function __construct(Permission $model)
    {
        $this->model = $model;
    }

    /**
     * @return array
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function index(): array
    {
        $models = $this->model->tableHasPermissions();

        return [
            'rows' => PermissionResource::collection($models)
        ];
    }
}
