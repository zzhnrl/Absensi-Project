<?php

namespace Database\Seeders;

use App\Models\StatusCuti;
use Illuminate\Database\Seeder;
use App\Helpers\Generate;

class StatusCutiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $StatusCuti = [
            [
                'uuid' => Generate::uuid(),
                'nama' => 'Diajukan',
                'kode' => 'S01',
                'deskripsi' => 'Status diajukan',
            ],

            [
                'uuid' => Generate::uuid(),
                'nama' => 'Disetujui',
                'kode' => 'S02',
                'deskripsi' => 'Status disetujui',
            ],

            [
                'uuid' => Generate::uuid(),
                'nama' => 'Ditolak',
                'kode' => 'S03',
                'deskripsi' => 'Status ditolak',
            ],
        ];

        foreach ($StatusCuti as $SA) {
            app('StoreStatusCutiService')->execute([
                'nama' => $SA['nama'],
                'kode' => $SA['kode'],
                'deskripsi' => $SA['deskripsi'],
            ]);
        }
    }
}
