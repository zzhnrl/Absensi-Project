<?php

namespace App\Services\Cuti;

use App\Models\UserInformation;
use App\Models\Cuti;
use App\Models\User;
use App\Services\DefaultService;
use App\Services\ServiceInterface;
use App\Rules\ExistsUuid;
use Illuminate\Validation\ValidationException;

class StoreCutiService extends DefaultService implements ServiceInterface
{
    public function __construct() {}

    public function process($dto)
    {
        $dto = $this->prepare($dto);

        $cuti = new Cuti();
        $cuti->user_id = $dto['user_id'];
        $cuti->status_cuti_id = $dto['status_cuti_id'];
        $cuti->nama_karyawan = $dto['nama_karyawan'];
        $cuti->tanggal_mulai = $dto['tanggal_mulai'];
        $cuti->tanggal_akhir = $dto['tanggal_akhir'];
        $cuti->keterangan = $dto['keterangan'] ?? null;

        $this->prepareAuditActive($cuti);
        $this->prepareAuditInsert($cuti);
        $cuti->save();

        $this->results['data'] = $cuti;
        $this->results['message'] = "Cuti successfully created";
    }

    public function prepare($dto)
    {
        if (isset($dto['user_uuid'])) {
            $userId = $this->findIdByUuid(User::query(), $dto['user_uuid']);

            if (!$userId) {
                throw ValidationException::withMessages([
                    'user_uuid' => ['User tidak ditemukan berdasarkan UUID yang diberikan.'],
                ]);
            }

            $dto['user_id'] = $userId;
        }

        return $dto;
    }

    public function rules($dto)
    {
        return [
            'user_uuid' => ['required', 'uuid', new ExistsUuid('users')],
            'nama_karyawan' => ['required'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_akhir' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'keterangan' => ['nullable'],
        ];
    }
}
