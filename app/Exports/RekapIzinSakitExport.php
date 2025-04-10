<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RekapIzinSakitExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return DB::table('rekap_izin_sakits')
            ->select('nama_karyawan', 'bulan', 'tahun', 'jumlah_izin_sakit')
            ->where('is_active', 1)
            ->get();
    }

    public function headings(): array
    {
        return [
            'Nama Karyawan',
            'Bulan',
            'Tahun',
            'Jumlah Izin/Sakit',
        ];
    }
}

