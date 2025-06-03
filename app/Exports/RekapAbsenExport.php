<?php

namespace App\Exports;

use App\Models\RekapAbsen;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RekapAbsenExport implements FromCollection, WithMapping, WithHeadings
{
    public function collection()
    {
        return RekapAbsen::whereNull('deleted_at')->get();
    }

    public function map($rekapabsen): array
    {
        return [
            $rekapabsen->uuid,
            $rekapabsen->nama_karyawan,
            $rekapabsen->wfo,
            $rekapabsen->wfh,
            $absensi->jumlah_point,
        ];
    }

    public function headings(): array
    {
        return [
            'UUID',
            'Nama Karyawan',
            'WFO',
            'WFH',
            'Jumlah Point',
        ];
    }
}

