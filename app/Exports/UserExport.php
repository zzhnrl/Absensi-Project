<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UserExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return DB::table('users')
            ->join('user_informations', 'users.id', '=', 'user_informations.user_id')
            ->leftJoin('user_roles', 'users.id', '=', 'user_roles.user_id')
            ->leftJoin('roles', 'user_roles.role_id', '=', 'roles.id')
            ->select(
                'user_informations.nama',
                'users.email',
                'user_informations.notlp',
                'user_informations.alamat',
                'roles.name as role'
            )
            ->get();
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Email',
            'No HP',
            'Alamat',
            'Role'
        ];
    }
}
