<?php

namespace Database\Seeders;

use Exception;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Modules\Roles\Entities\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tables = Permission::tableHasPermissions();
        foreach ($tables as $table) {
            $this->handle($table, ['read', 'create', 'update', 'delete']);
        }
    }

    public function handle($table, $roles)
    {
        try {
            $permissions = [];
            foreach ($roles as $role) {
                if (Permission::where('name', $role . '_' . $table)->doesntExist()) {
                    $permissions[] = [
                        'name'          => $role . '_' . $table,
                        'guard_name'    => 'api',
                        'created_at'    => Carbon::now()
                    ];
                }
            }
            if (count($permissions)) {
                Permission::insert($permissions);
            }
        } catch (Exception $e) {
            Log::warning($e);
        }
    }
}
