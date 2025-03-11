<?php

namespace App\Services\RekapIzinSakit;

use App\Models\UserInformation;
use App\Models\RekapIzinSakit;
use App\Models\FileStorage;
use App\Models\User;
use App\Services\DefaultService;
use App\Services\ServiceInterface;
use App\Rules\ExistsUuid;

class StoreRekapIzinSakitService extends DefaultService implements ServiceInterface
{
    public function __construct() {}

    public function process($dto)
    {
        $dto = $this->prepare($dto);

        $rekap_izin_sakit = new RekapIzinSakit();

        $rekap_izin_sakit->user_id = $dto['user_id'];
        $rekap_izin_sakit->nama_karyawan = $dto['nama_karyawan'];
        $rekap_izin_sakit->bulan = $dto['bulan'];
        $rekap_izin_sakit->tahun = $dto['tahun'];
        $rekap_izin_sakit->jumlah_izin_sakit = $dto['jumlah_izin_sakit'];

        $this->prepareAuditActive($rekap_izin_sakit);
        $this->prepareAuditInsert($rekap_izin_sakit);
        $rekap_izin_sakit->save();

        $this->results['data'] = $rekap_izin_sakit;
        $this->results['message'] = "Rekap izin sakit successfully created";
    }

    public function prepare($dto)
    {
        if (isset($dto['user_uuid'])) {
            $dto['user_id'] = $this->findIdByUuid(User::query(), $dto['user_uuid']);
        }

        return $dto;
    }

    public function rules($dto)
    {
        return [
            'user_uuid' => ['required', 'uuid', new ExistsUuid('users')],
            'nama_karyawan' => ['required'],
            'bulan' => ['required'],
            'tahun' => ['required'],
            'jumlah_izin_sakit' => ['nullable'],
        ];
    }
}