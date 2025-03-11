<?php

namespace App\Services\User;

use App\Models\User;
use App\Rules\ExistsUuid;
use App\Services\DefaultService;
use App\Services\ServiceInterface;

class DeleteUserService extends DefaultService implements ServiceInterface
{
    public function process($dto)
    {
        $dto = $this->prepare($dto);

        $user = User::find($dto['user_id']);

        $this->results['message'] = "User successfully deleted";
        $this->results['data'] = $this->activeAndRemoveData($user, $dto);
    }

    public function prepare($dto)
    {
        if (isset($dto['user_uuid'])) {
            $dto['user_id'] = $this->findIdByUuid(User::query(), $dto['user_uuid']);
        }
        return $dto;
    }

    public function rules($dto)
    {
        return [
            'user_uuid' => ['required', 'uuid', new ExistsUuid('users')]
        ];
    }
}
