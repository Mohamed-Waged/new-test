<?php

namespace Modules\Roles\Entities;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    /**
     * The table associated with the model.
     * @var string $table
     */
    protected $table = 'permissions';

    /**
     * The attributes that are mass assignable. That means these attributes can be
     * passed to class constructor as array and attributes will be set automatically.
     * @var array $fillable
     */
    protected $fillable = ['id', 'name', 'guard_name'];

    /**
     * @param string $table
     * @return array
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public static function getAccessPermissions($table): array
    {
        return [
            'read'    => self::hasAuthority('read_'.$table) ?? false,
            'create'  => self::hasAuthority('create_'.$table) ?? false,
            'update'  => self::hasAuthority('update_'.$table) ?? false,
            'delete'  => self::hasAuthority('delete_'.$table) ?? false
        ];
    }

    /**
     * @param string $table
     * @return bool
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public static function hasAuthority($table): bool
    {
        if (auth('api')->check()) {
            $isAdmin = auth('api')->user()->roles()->first()->name;
            if ($isAdmin == 'admin' || $isAdmin == 'root') {
                $response = true;
            } else {
                $row = self::where('name', $table)->first();
                if ($row) {
                    $response = DB::table('role_has_permissions')
                                        ->where('permission_id', $row->id)
                                        ->where('role_id', auth('api')->user()->roles()->first()->id)
                                        ->exists() ?? false;
                }
            }

            return $response ?? false;
        }

        return false;
    }

    /**
     * @param string $permission
     * @param string $type
     * @return int
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public static function getId($permission, $type): int
    {
        if ($permission[$type] == true) {
            $response = self::where('name', $type . '_' . $permission['name'])->first()->id;
        }

        return $response ?? 0;
    }

    /**
     * @return array
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public static function tableHasPermissions(): array
    {
        $migrations = DB::table('migrations')->where('migration', 'like', '%create_%')->get();
        foreach ($migrations as $migrate) {

            // skip field_jobs table & some tables....
            if (strpos($migrate->migration, 'failed_jobs_table') !== false) { }
            else if (strpos($migrate->migration, 'password_resets') !== false) { }
            else if (strpos($migrate->migration, 'personal_access_tokens') !== false) { }
            else if (strpos($migrate->migration, 'imageables') !== false) { }
            else if (strpos($migrate->migration, 'wallets') !== false) { }
            // feel free to add any restirected tables...

            else {
                // extract create_ and _table from migrations
                $tables[] = strtolower(explode('_table', explode('create_', $migrate->migration)[1])[0]);
            }

        }

        return $tables ?? [];
    }

    /**
     * @param int $roleId
     * @return array
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public static function getPermissions($roleId): array
    {
        $tables = self::tableHasPermissions();
        foreach ($tables as $table) {
            $response[] = [
                'name'      => $table,
                'read'      => self::hasPermissionAccess($table, $roleId, 'read'),
                'create'    => self::hasPermissionAccess($table, $roleId, 'create'),
                'update'    => self::hasPermissionAccess($table, $roleId, 'update'),
                'delete'    => self::hasPermissionAccess($table, $roleId, 'delete')
            ];
        }
        return $response ?? [];
    }

    /**
     * @param string $table
     * @param int $roleId
     * @param string $type
     * @return bool
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public static function hasPermissionAccess($table, $roleId, $type): bool
    {
        if ($roleId) {
            $permissions = DB::table('role_has_permissions as rhp')
                                    ->leftjoin('permissions as p', 'rhp.permission_id', '=', 'p.id')
                                    ->where('rhp.role_id', $roleId)
                                    ->get();

            foreach ($permissions as $permission) {
                if ($permission->name == $type . '_' . strtolower($table)) {
                    $response = true;
                }
            }
        }

        return $response ?? false;
    }

    /**
     * @param int $roleId
     * @return array
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public static function getPermissionsByRoleId($roleId): array
    {
        $permissionsIds = DB::table('role_has_permissions')
                                    ->where('role_id', $roleId)
                                    ->pluck('permission_id');
        $permissions    = DB::table('permissions')
                                    ->whereIN('id', $permissionsIds)
                                    ->where('name', 'like', '%read_%')
                                    ->get();

        $views = [];
        foreach ($permissions as $permission) {
            $views[] = explode('read_', $permission->name)[1];
        }

        return $views;
    }
}
