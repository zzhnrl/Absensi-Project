<?php

namespace App\Services\PointUser;

use App\Models\PointUser;
use App\Rules\ExistsUuid;
use App\Services\DefaultService;
use App\Services\ServiceInterface;

class DeletePointUserService extends DefaultService implements ServiceInterface
{
    public function process($dto)
    {
        $dto = $this->prepare($dto);

        $point_user = PointUser::find($dto['point_user_id']);

        $this->results['message'] = "Point user successfully deleted";
        $this->results['data'] = $this->activeAndRemoveData($point_user, $dto);
    }

    public function prepare($dto)
    {
        if (isset($dto['point_user_uuid'])) {
            $dto['point_user_id'] = $this->findIdByUuid(PointUser::query(), $dto['point_user_uuid']);
        }
        return $dto;
    }

    public function rules($dto)
    {
        return [
            'point_user_uuid' => ['required', 'uuid', new ExistsUuid('point_users')]
        ];
    }
}
