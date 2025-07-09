<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class KategoriAbsensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $KategoriAbsensi = [
            [
                'name' => 'WFH',
                'code' => 'W1',
                'point' => 4,
                'description' => 'Work from Home merupakan absensi kehadiran karyawan yang bekerja di rumah.',
            ],
            [
                'name' => 'WFO',
                'code' => 'W3',
                'point' => 6,
                'description' => 'Work from Office merupakan absensi kehadiran karyawan yang berkerja datang ke kantor.'
            ],
            [
                'name' => 'Alpha',
                'code' => 'A1',
                'point' => 0,
                'description' => 'Alpha merupakan absensi kehadiran karyawan yang tidak hadir tanpa izin.'
            ]
        ];
        foreach ($KategoriAbsensi as $KA) {
            app('StoreKategoriAbsensiService')->execute([
                'name' => $KA['name'],
                'code' => $KA['code'],
                'point' => $KA['point'],
                'description' => $KA['description'],
            ]);
        }
    }
}
