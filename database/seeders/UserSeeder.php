<?php

namespace Database\Seeders;

use App\Helpers\Generate;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::insert([
            [
                'uuid' => Generate::uuid(),
                'email' => 'developer@wangun.co',
                'password' => bcrypt('password')
            ],
                        [
                'uuid' => Generate::uuid(),
                'email' => 'karyawan@gmail.com',
                'password' => bcrypt('12345678')
            ],
        ]);
    }
}
