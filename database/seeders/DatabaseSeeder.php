<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\RootSeeder;
use Database\Seeders\PermissionSeeder;
use Modules\Settings\Database\Seeders\SettingsDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10000)->create();
        $this->call(PermissionSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(RootSeeder::class);

        // Modules
        $this->call(SettingsDatabaseSeeder::class);
    }
}
