<?php

namespace App\Services\Cuti;

use App\Models\Cuti;
use App\Models\StatusCuti;
use App\Services\DefaultService;
use App\Services\ServiceInterface;
use App\Rules\ExistsUuid;

class UpdateCutiService extends DefaultService implements ServiceInterface
{

    public function process($dto)
    {
        $dto = $this->prepare($dto);
        $cuti = Cuti::find($dto['cuti_id']);

        if (!$cuti) {
            throw new \Exception("Cuti not found");
        }

        $cuti->status_cuti_id = $dto['status_cuti_id'] ?? $cuti->status_cuti_id;
        $cuti->nama_karyawan = $dto['nama_karyawan'] ?? $cuti->nama_karyawan;
        $cuti->tanggal_mulai = $dto['tanggal_mulai'] ?? $cuti->tanggal_mulai;
        $cuti->tanggal_akhir = $dto['tanggal_akhir'] ?? $cuti->tanggal_akhir;
        $cuti->keterangan = $dto['keterangan'] ?? $cuti->keterangan;
        $cuti->approve_at = $dto['approve_at'] ?? $cuti->approve_at;
        $cuti->approve_by = $dto['approve_by'] ?? $cuti->approve_by;
        $cuti->reject_at = $dto['reject_at'] ?? $cuti->reject_at;
        $cuti->reject_by = $dto['reject_by'] ?? $cuti->reject_by;

        $this->prepareAuditUpdate($cuti);
        $cuti->save();

        $this->results['data'] = $cuti;
        $this->results['message'] = "Cuti successfully updated";
    }

    public function prepare($dto)
    {
        if (isset($dto['cuti_uuid'])) {
            $dto['cuti_id'] = $this->findIdByUuid(Cuti::query(), $dto['cuti_uuid']);
        }

        if (isset($dto['status_cuti_uuid'])) {
            $dto['status_cuti_id'] = $this->findIdByUuid(StatusCuti::query(), $dto['status_cuti_uuid']);
        }

        return $dto;
    }

    public function rules($dto)
    {
        return [
            'cuti_uuid' => ['required', 'uuid', new ExistsUuid('cutis')],
            'status_cuti_id' => ['nullable'],
            'nama_karyawan' => ['nullable'],
            'tanggal_mulai' => ['nullable'],
            'tanggal_akhir' => ['nullable'],
            'keterangan' => ['nullable']
        ];
    }
}
