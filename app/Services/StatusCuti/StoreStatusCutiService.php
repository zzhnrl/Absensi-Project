<?php

namespace App\Services\StatusCuti;

use App\Models\StatusCuti;
use App\Rules\UniqueData;
use App\Services\DefaultService;
use App\Services\ServiceInterface;

class StoreStatusCutiService extends DefaultService implements ServiceInterface
{

    public function process($dto)
    {
        $dto = $this->prepare($dto);
        $status_cuti = new StatusCuti();

        $status_cuti->nama = $dto['nama'];
        $status_cuti->kode = $dto['kode'] ?? null;
        $status_cuti->deskripsi = $dto['deskripsi'] ?? null;

        $this->prepareAuditActive($status_cuti);
        $this->prepareAuditInsert($status_cuti);
        $status_cuti->save();

        $this->results['data'] = $status_cuti;
        $this->results['message'] = "status cuti successfully stored";
    }

    public function prepare($dto)
    {
        return $dto;
    }

    public function rules($dto)
    {
        return [
            'nama' => ['required', new UniqueData('status_cutis', 'nama')],
            'kode' => ['nullable', new UniqueData('status_cutis', 'kode')],
            'deskripsi' => ['nullable']
        ];
    }
}
