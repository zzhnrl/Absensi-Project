<?php

namespace App\Services\StatusCuti;

use App\Models\StatusCuti;
use App\Rules\ExistsUuid;
use App\Services\DefaultService;
use App\Services\ServiceInterface;

class DeleteStatusCutiService extends DefaultService implements ServiceInterface
{
    public function process($dto)
    {
        $dto = $this->prepare($dto);

        $status_cuti = StatusCuti::find($dto['status_cuti_id']);

        $this->results['message'] = "Role successfully deleted";
        $this->results['data'] = $this->activeAndRemoveData($status_cuti, $dto);
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
            'status_cuti_uuid' => ['required', 'uuid', new ExistsUuid('status_cutis')]
        ];
    }
}
