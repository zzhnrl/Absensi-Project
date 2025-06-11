<?php

namespace App\Exports;

use App\Models\Absensi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RekapAbsenExport implements FromCollection, WithMapping, WithHeadings
{
    public function collection()
    {
        return Absensi::whereNull('deleted_at')->get();
    }

    public function map($absensi): array
    {
        return [
            $absensi->uuid,
            $absensi->nama_karyawan,
            $absensi->nama_kategori === 'WFO' ? 'WFO' : '',
            $absensi->nama_kategori === 'WFH' ? 'WFH' : '',
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
