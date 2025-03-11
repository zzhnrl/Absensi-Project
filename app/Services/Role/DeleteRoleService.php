<?php

namespace App\Services\Role;

use App\Models\Role;
use App\Rules\ExistsUuid;
use App\Services\DefaultService;
use App\Services\ServiceInterface;

class DeleteRoleService extends DefaultService implements ServiceInterface
{
    public function process($dto)
    {
        $dto = $this->prepare($dto);

        $role = Role::find($dto['role_id']);

        $this->results['message'] = "Role successfully deleted";
        $this->results['data'] = $this->activeAndRemoveData($role, $dto);
    }

    public function prepare($dto)
    {
        if (isset($dto['role_uuid'])) {
            $dto['role_id'] = $this->findIdByUuid(Role::query(), $dto['role_uuid']);
        }
        return $dto;
    }

    public function rules($dto)
    {
        return [
            'role_uuid' => ['required', 'uuid', new ExistsUuid('roles')]
        ];
    }
}
