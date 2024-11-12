<?php

namespace Database\Seeders;

use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Modules\Roles\Entities\Role;
use Modules\Roles\Entities\Permission;
use Modules\Roles\Entities\RoleHasPermission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {

            // get permissions
            $permissions = Permission::get();

            // root
            $root = Role::firstOrCreate([
                'name'       => 'root',
                'guard_name' => 'api'
            ]);

            // super admin
            $admin = Role::firstOrCreate([
                'name'       => 'admin',
                'guard_name' => 'api'
            ]);

            $rootPermissions = $adminPermissions = [];
            foreach ($permissions as $permission) {

                if (RoleHasPermission::where('permission_id', $permission->id)
                    ->where('role_id', $root->id)
                    ->doesntExist()
                ) {
                    $rootPermissions[] = [
                        'permission_id' => $permission->id,
                        'role_id'       => $root->id
                    ];
                }

                if (RoleHasPermission::where('permission_id', $permission->id)
                    ->where('role_id', $admin->id)
                    ->doesntExist()
                ) {
                    $adminPermissions[] = [
                        'permission_id' => $permission->id,
                        'role_id'       => $admin->id
                    ];
                }
            }
            RoleHasPermission::insert($rootPermissions);
            RoleHasPermission::insert($adminPermissions);


            // Lecturer
            if (Role::where('name', 'lecturer')->doesntExist()) {
                Role::create([
                    'name'       => 'lecturer',
                    'guard_name' => 'api'
                ]);
            }

            // Member
            if (Role::where('name', 'member')->doesntExist()) {
                Role::create([
                    'name'       => 'member',
                    'guard_name' => 'api'
                ]);
            }
        } catch (Exception $e) {
            Log::warning($e);
        }
    }
}
