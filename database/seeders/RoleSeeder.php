<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use App\Helpers\Generate;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $Role = [
            [
                'uuid' => Generate::uuid(),
                'name' => 'Master Admin',
                'code' => 'R01',
                'description' => 'Ini merupakan master admin'
            ],

            [
                'uuid' => Generate::uuid(),
                'name' => 'Manajer',
                'code' => 'R02',
                'description' => 'Ini merupakan manajer'
            ],

            [
                'uuid' => Generate::uuid(),
                'name' => 'Karyawan',
                'code' => 'R03',
                'description' => 'Ini merupakan karyawan'
            ],
        ];

        foreach ($Role as $RO) {
            app('StoreRoleService')->execute([
                'name' => $RO['name'],
                'code' => $RO['code'],
                'description' => $RO['description'],
            ]);
        }
    }
}
