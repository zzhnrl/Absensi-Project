<?php

namespace Database\Seeders;

use App\Models\UserInformation;
use Illuminate\Database\Seeder;
use App\Helpers\Generate;

class UserInformationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        UserInformation::insert([
            [
                'uuid' => Generate::uuid(),
                'user_id' => '12',
                'signature_file_id' => null,
                'nama' => 'Master Admin',
                'notlp' => '081364792547',
                'alamat' => 'Testing',
            ],
                        [
                'uuid' => Generate::uuid(),
                'user_id' => '13',
                'signature_file_id' => null,
                'nama' => 'Karyawan',
                'notlp' => '081364792547',
                'alamat' => 'Testing',
            ],
        ]);
    }
}
