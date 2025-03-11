<?php

namespace Database\Seeders;

use App\Helpers\Generate;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $permissions = config('permission');

        foreach ($permissions as $permission) {
            Permission::create([
                'uuid' => Generate::uuid(),
                'module_key' => Generate::toSnakeCase( strtolower($permission['module_name']) . " " . strtolower($permission['permission_name']) ),
                'module_name' => $permission['module_name'],
                'permission_name' => $permission['permission_name'],
            ]);
        }
    }
}
