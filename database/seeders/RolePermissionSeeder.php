<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\RolePermission;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $count_permission = Permission::count();
        for ($i = 1; $i <= $count_permission; $i++) {
            RolePermission::insert([
                [
                    'role_id' => 1,
                    'permission_id' => $i,
                ],
            ]);
        }
    }
}
