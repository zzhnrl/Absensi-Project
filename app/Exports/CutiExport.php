<?php

namespace App\Exports;

use App\Models\Cuti;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CutiExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Cuti::whereNull('deleted_at')->get();
    }

    public function map($row): array
    {
        return [
            $row->uuid,
            $row->nama_karyawan,
            $row->tanggal_mulai,
            $row->tanggal_akhir,
            $row->keterangan,
            $row->approve_at,
            $row->approve_by,
            $row->reject_at,
            $row->reject_by,
        ];
    }

    public function headings(): array
    {
        return [
            'UUID',
            'Nama Karyawan',
            'Tanggal Mulai',
            'Tanggal Akhir',
            'Keterangan',
            'Disetujui Pada',
            'Disetujui Oleh',
            'Ditolak Pada',
            'Ditolak Oleh',
        ];
    }
}

