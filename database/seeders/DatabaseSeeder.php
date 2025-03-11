<?php

namespace Database\Seeders;

use App\Models\AssetBrand;
use App\Models\GlobalParameter;
use App\Models\ReminderType;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RoleSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(UserInformationSeeder::class);
        $this->call(UserRoleSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(RolePermissionSeeder::class);
        $this->call(KategoriAbsensiSeeder::class);
        $this->call(StatusCutiSeeder::class);
    }
}
