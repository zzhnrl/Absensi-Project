<?php

namespace App\Services\Absensi;

use App\Models\UserInformation;
use App\Models\Absensi;
use App\Models\User;
use App\Models\KategoriAbsensi;
use App\Services\DefaultService;
use App\Services\ServiceInterface;
use App\Rules\ExistsUuid;

class StoreAbsensiService extends DefaultService implements ServiceInterface
{
    public function __construct() {}

    public function process($dto)
    {
        $dto = $this->prepare($dto);

        $absensi = new Absensi();
        $absensi->user_id = $dto['user_id'];
        $absensi->kategori_absensi_id = $dto['kategori_absensi_id'];
        $absensi->nama_karyawan = $dto['nama_karyawan'];
        $absensi->nama_kategori = $dto['nama_kategori'];
        $absensi->tanggal = $dto['tanggal'];
        $absensi->keterangan = $dto['keterangan'];
        $absensi->jumlah_point = $dto['jumlah_point'];

        $this->prepareAuditActive($absensi);
        $this->prepareAuditInsert($absensi);
        $absensi->save();

        $this->results['data'] = $absensi;
        $this->results['message'] = "Absensi successfully created";
    }

    public function prepare($dto)
    {
        if (isset($dto['user_uuid'])) {
            $dto['user_id'] = $this->findIdByUuid(User::query(), $dto['user_uuid']);
        }

        if (isset($dto['kategori_absensi_uuid'])) {
            $dto['kategori_absensi_id'] = $this->findIdByUuid(KategoriAbsensi::query(), $dto['kategori_absensi_uuid']);
        }

        return $dto;
    }

    public function rules($dto)
    {
        return [
            'user_uuid' => ['required', 'uuid', new ExistsUuid('users')],
            'kategori_absensi_uuid' => ['required', 'uuid', new ExistsUuid('kategori_absensis')],
            'nama_karyawan' => ['required'],
            'nama_kategori' => ['required'],
            'tanggal' => ['required'],
            'keterangan' => ['nullable'],
            'jumlah_point' => ['required'],
        ];
    }
}
