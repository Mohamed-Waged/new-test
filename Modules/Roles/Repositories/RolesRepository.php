<?php

namespace Modules\Roles\Repositories;

use Modules\Roles\Entities\Role;
use Modules\Roles\Entities\Permission;
use App\Repositories\BaseRepository;
use Modules\Roles\Http\Resources\RoleResource;
use Modules\Roles\Http\Resources\RoleShowResource;
use Modules\Roles\Http\Resources\RoleKeyValueResource;
use Modules\Roles\Repositories\Contracts\RolesRepositoryInterface;
use Modules\Roles\Repositories\Contracts\PermissionsRepositoryInterface;

class RolesRepository extends BaseRepository implements RolesRepositoryInterface
{
    protected $model;
    protected $permissionModel;
    protected $permissionRepository;

    /**
     * @param Role $model
     * @param Permission $permissionModel
     * @param PermissionsRepositoryInterface $permissionRepository
     * @return void
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function __construct(
        Role $model,
        Permission $permissionModel,
        PermissionsRepositoryInterface $permissionRepository
    ) {
        $this->model = $model;
        $this->permissionModel = $permissionModel;
        $this->permissionRepository = $permissionRepository;
    }

    /**
     * @param array $request
     * @return array
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function index($request): array
    {
        $rows = $this->model
            ->select('id', 'name', 'is_active', 'created_at')
            ->whereNOTIN('name', ['root'])
            ->when(!empty($request['search']), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request['search'] . '%');
            })
            ->when(!empty($request['whereNotIn']), function ($query) use ($request) {
                $query->whereNotIN('name', $request['whereNotIn']);
            })
            ->when(!empty($request['date']), function ($query) use ($request) {
                $query->whereDate('created_at', $request['date']);
            })
            ->when(!empty($request['status']), function ($query) use ($request) {
                $query->whereIsActive(strtolower(($request['status']) == 'active') ? true : false);
            })
            ->latest('id')
            ->get();

        return [
            'rows'          => RoleResource::collection($rows),
            'permissions'   => Permission::getAccessPermissions('permissions')
        ];
    }

    /**
     * @param array $request
     * @param mixed $id
     * @return mixed
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function createOrUpdate($request, $id = NULL): mixed
    {
        try {

            $row                = (isset($id)) ? $this->model::find(decrypt($id)) : new $this->model;
            $row->name          = $request['role'];
            $row->guard_name    = 'api';
            $row->is_active     = true;
            $row->save();

            // Permissions
            if (!empty($request['rolePermissions'])) {
                $row->roleHasPermissions()->delete();
                $permissionsIds = [];
                $types = ['read', 'create', 'update', 'delete'];

                foreach ($request['rolePermissions']['permissions'] as $permission) {
                    foreach ($types as $type) {
                        if ($this->permissionModel->getId($permission, $type)) {
                            $permissionsIds[] = [
                                'permission_id' => $this->permissionModel->getId($permission, $type),
                                'role_id'       => $row->id
                            ];
                        }
                    }
                }

                if (count($permissionsIds)) {
                    $row->roleHasPermissions()->insert($permissionsIds);
                }
            }

            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param mixed $id
     * @return array
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function find($id): array
    {
        $row = $this->model
            ->select('id', 'name', 'is_active')
            ->where('id', decrypt($id))
            ->first();

        return [
            new RoleShowResource($row)
        ];
    }

    /**
     * @param mixed $id
     * @return mixed
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function destroy($id): mixed
    {
        try {
            $this->model->where('id', decrypt($id))->delete();
            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @return array
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function keyValue(): array
    {
        $rows = $this->model::whereIsActive(true)->get();

        return [
            'rows' => RoleKeyValueResource::collection($rows)
        ];
    }
}
