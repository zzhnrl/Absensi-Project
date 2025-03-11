<?php

namespace App\Services\KategoriAbsensi;

use App\Models\KategoriAbsensi;
use App\Rules\UniqueData;
use App\Services\DefaultService;
use App\Services\ServiceInterface;

class StoreKategoriAbsensiService extends DefaultService implements ServiceInterface
{

    public function process($dto)
    {
        $dto = $this->prepare($dto);
        $kategori_absensi = new KategoriAbsensi();

        $kategori_absensi->name = $dto['name'];
        $kategori_absensi->code = $dto['code'] ?? null;
        $kategori_absensi->point = $dto['point'] ?? null;
        $kategori_absensi->description = $dto['description'] ?? null;

        $this->prepareAuditActive($kategori_absensi);
        $this->prepareAuditInsert($kategori_absensi);
        $kategori_absensi->save();

        $this->results['data'] = $kategori_absensi;
        $this->results['message'] = "Kategori absensi successfully stored";
    }

    public function prepare($dto)
    {
        return $dto;
    }

    public function rules($dto)
    {
        return [
            'name' => ['required', new UniqueData('kategori_absensis', 'name')],
            'code' => ['nullable', new UniqueData('kategori_absensis', 'code')],
            'description' => ['nullable']
        ];
    }
}
