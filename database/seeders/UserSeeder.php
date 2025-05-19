<?php

namespace Database\Seeders;

use App\Helpers\Generate;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Crypt;

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
                'email' => 'nuranurulazizah@gmail.com',
                'role_id' => '1',
                'password' => Crypt::encryptString('password')
            ],
                        [
                'uuid' => Generate::uuid(),
                'email' => 'zizahnurazizahh@gmail.com',
                'role_id' => '3',
                'password' => Crypt::encryptString('12345678'),
            ],
            
        ]);
    }
}
