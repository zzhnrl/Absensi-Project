<?php

namespace App\Services\IzinSakit;

use App\Models\UserInformation;
use App\Models\IzinSakit;
use App\Models\FileStorage;
use App\Models\User;
use App\Services\DefaultService;
use App\Services\ServiceInterface;
use App\Rules\ExistsUuid;
use App\Rules\MaxFileSize;

class StoreIzinSakitService extends DefaultService implements ServiceInterface
{
    public function __construct() {}

    public function process($dto)
    {
        $dto = $this->prepare($dto);

        $izin_sakit = new IzinSakit();

        if(isset($dto['photo_id'])) {
            FileStorage::find($dto['photo_id'])->update([
                "is_used" => true
            ]);
        }

        $izin_sakit->photo_id = $dto['photo_id'] ?? null;
        $izin_sakit->user_id = $dto['user_id'];
        $izin_sakit->nama_karyawan = $dto['nama_karyawan'];
        $izin_sakit->tanggal = $dto['tanggal'];
        $izin_sakit->keterangan = $dto['keterangan'];

        $this->prepareAuditActive($izin_sakit);
        $this->prepareAuditInsert($izin_sakit);
        $izin_sakit->save();

        $this->results['data'] = $izin_sakit;
        $this->results['message'] = "Izin sakit successfully created";
    }

    public function prepare($dto)
    {
        if (isset($dto['user_uuid'])) {
            $dto['user_id'] = $this->findIdByUuid(User::query(), $dto['user_uuid']);
        }

        if (isset($dto['photo_uuid'])) {
            $dto['photo_id'] = $this->findIdByUuid(FileStorage::query(), $dto['photo_uuid']);
        }

        return $dto;
    }

    public function rules($dto)
    {
        return [
            'photo_uuid' => ['required', 'uuid', new ExistsUuid('file_storages'), new MaxFileSize(10)],
            'user_uuid' => ['required', 'uuid', new ExistsUuid('users')],
            'nama_karyawan' => ['required'],
            'tanggal' => ['required'],
            'keterangan' => ['nullable'],
        ];
    }
}