<?php

namespace App\Services\UserRole;

use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use App\Rules\ExistsUuid;
use App\Services\DefaultService;
use App\Services\ServiceInterface;

class RemoveUserRoleService extends DefaultService implements ServiceInterface
{
    public function process($dto)
    {
        $dto = $this->prepare($dto);

        UserRole::where('role_id', $dto['role_id'])->where('user_id', $dto['user_id'])->delete();

        $this->results['data'] = [];
        $this->results['message'] = "Role successfully removed from user";
    }

    public function prepare($dto)
    {
        $dto['user_id'] = $this->findIdByUuid(User::query(), $dto['user_uuid']);
        $dto['role_id'] = $this->findIdByUuid(Role::query(), $dto['role_uuid']);
        return $dto;
    }

    public function rules($dto)
    {
        return [
            'user_uuid' => ['required', 'uuid', new ExistsUuid('users')],
            'role_uuid' => ['required', 'uuid', new ExistsUuid('roles')],
        ];
    }
}
