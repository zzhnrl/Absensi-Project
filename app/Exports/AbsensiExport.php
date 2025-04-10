<?php

namespace App\Exports;

use App\Models\Absensi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AbsensiExport implements FromCollection, WithMapping, WithHeadings
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
            $absensi->nama_kategori,
            $absensi->tanggal,
            $absensi->keterangan ?? '-',
            $absensi->jumlah_point,
        ];
    }

    public function headings(): array
    {
        return [
            'UUID',
            'Nama Karyawan',
            'Kategori',
            'Tanggal',
            'Keterangan',
            'Jumlah Point',
        ];
    }
}

