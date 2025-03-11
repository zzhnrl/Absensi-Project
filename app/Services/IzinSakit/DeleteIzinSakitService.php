<?php

namespace App\Services\IzinSakit;

use App\Models\IzinSakit;
use App\Rules\ExistsUuid;
use App\Services\DefaultService;
use App\Services\ServiceInterface;

class DeleteIzinSakitService extends DefaultService implements ServiceInterface
{
    public function process($dto)
    {
        $dto = $this->prepare($dto);

        $izin_sakit = IzinSakit::find($dto['izin_sakit_id']);

        $this->results['message'] = "Izin sakit successfully deleted";
        $this->results['data'] = $this->activeAndRemoveData($izin_sakit, $dto);
    }

    public function prepare($dto)
    {
        if (isset($dto['izin_sakit_uuid'])) {
            $dto['izin_sakit_id'] = $this->findIdByUuid(IzinSakit::query(), $dto['izin_sakit_uuid']);
        }
        return $dto;
    }

    public function rules($dto)
    {
        return [
            'izin_sakit_uuid' => ['required', 'uuid', new ExistsUuid('izin_sakits')]
        ];
    }
}
