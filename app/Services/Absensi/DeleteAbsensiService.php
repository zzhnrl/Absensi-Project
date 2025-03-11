<?php

namespace App\Services\Absensi;

use App\Models\Absensi;
use App\Rules\ExistsUuid;
use App\Services\DefaultService;
use App\Services\ServiceInterface;

class DeleteAbsensiService extends DefaultService implements ServiceInterface
{
    public function process($dto)
    {
        $dto = $this->prepare($dto);

        $absensi = Absensi::find($dto['absensi_id']);

        $this->results['message'] = "Absensi successfully deleted";
        $this->results['data'] = $this->activeAndRemoveData($absensi, $dto);
    }

    public function prepare($dto)
    {
        if (isset($dto['absensi_uuid'])) {
            $dto['absensi_id'] = $this->findIdByUuid(Absensi::query(), $dto['absensi_uuid']);
        }
        return $dto;
    }

    public function rules($dto)
    {
        return [
            'absensi_uuid' => ['required', 'uuid', new ExistsUuid('absensis')]
        ];
    }
}
