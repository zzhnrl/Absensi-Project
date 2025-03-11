<?php

namespace App\Services\KategoriAbsensi;

use App\Models\KategoriAbsensi;
use App\Services\DefaultService;
use App\Services\ServiceInterface;
use App\Rules\ExistsUuid;

class UpdateKategoriAbsensiService extends DefaultService implements ServiceInterface
{

    public function process($dto)
    {
        $dto = $this->prepare($dto);
        $kategori_absensi = KategoriAbsensi::find($dto['kategori_absensi_id']);

        if (!$kategori_absensi) {
            throw new \Exception("Kategori absensi not found");
        }

        $kategori_absensi->name = $dto['name'] ?? $kategori_absensi->name;
        $kategori_absensi->code = $dto['code'] ?? $kategori_absensi->code;
        $kategori_absensi->point = $dto['point'] ?? $kategori_absensi->point;
        $kategori_absensi->description = $dto['description'] ?? $kategori_absensi->description;

        $this->prepareAuditUpdate($kategori_absensi);
        $kategori_absensi->save();

        $this->results['data'] = $kategori_absensi;
        $this->results['message'] = "Kategori absensi successfully updated";
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
            'kategori_absensi_uuid' => ['required', 'uuid', new ExistsUuid('kategori_absensis')],
            'name' => ['required'],
            'code' => ['nullable'],
            'point' => ['required'],
            'description' => ['nullable']
        ];
    }
}
