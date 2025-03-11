<?php

namespace App\Services\StatusCuti;

use App\Services\DefaultService;
use App\Services\ServiceInterface;
use App\Models\StatusCuti;
use App\Rules\ExistsUuid;

class UpdateStatusCutiService extends DefaultService implements ServiceInterface
{

    public function process($dto)
    {
        $dto = $this->prepare($dto);
        $status_cuti = StatusCuti::find($dto['status_cuti_id']);

        if (!$status_cuti) {
            throw new \Exception("status_cuti not found");
        }

        $status_cuti->nama = $dto['nama'] ?? $status_cuti->nama;
        $status_cuti->kode = $dto['kode'] ?? $status_cuti->kode;
        $status_cuti->deskripsi = $dto['deskripsi'] ?? $status_cuti->deskripsi;

        $this->prepareAuditUpdate($status_cuti);
        $status_cuti->save();

        $this->results['data'] = $status_cuti;
        $this->results['message'] = "Status cuti successfully updated";
    }

    public function prepare($dto)
    {
        if (isset($dto['status_cuti_uuid'])) {
            $dto['status_cuti_id'] = $this->findIdByUuid(StatusCuti::query(), $dto['status_cuti_uuid']);
        }

        return $dto;
    }

    public function rules($dto)
    {
        return [
            'status_cuti_uuid' => ['required', 'uuid', new ExistsUuid('status_cutis')],
            'nama' => ['required'],
            'kode' => ['nullable'],
            'deskripsi' => ['nullable']
        ];
    }
}
