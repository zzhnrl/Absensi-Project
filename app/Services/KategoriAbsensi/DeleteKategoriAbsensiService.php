<?php

namespace App\Services\KategoriAbsensi;

use App\Models\KategoriAbsensi;
use App\Rules\ExistsUuid;
use App\Services\DefaultService;
use App\Services\ServiceInterface;

class DeleteKategoriAbsensiService extends DefaultService implements ServiceInterface
{
    public function process($dto)
    {
        $dto = $this->prepare($dto);

        $kategori_absensi = KategoriAbsensi::find($dto['kategori_absensi_id']);

        $this->results['message'] = "Kategori absensi successfully deleted";
        $this->results['data'] = $this->activeAndRemoveData($kategori_absensi, $dto);
    }

    public function prepare($dto)
    {
        if (isset($dto['kategori_absensi_uuid'])) {
            $dto['kategori_absensi_id'] = $this->findIdByUuid(KategoriAbsensi::query(), $dto['kategori_absensi_uuid']);
        }
        return $dto;
    }

    public function rules($dto)
    {
        return [
            'kategori_absensi_uuid' => ['required', 'uuid', new ExistsUuid('kategori_absensis')]
        ];
    }
}
