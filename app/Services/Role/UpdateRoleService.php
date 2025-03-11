<?php

namespace App\Services\Role;

use App\Services\DefaultService;
use App\Services\ServiceInterface;
use App\Models\Role;
use App\Rules\ExistsUuid;

class UpdateRoleService extends DefaultService implements ServiceInterface
{

    public function process($dto)
    {
        $dto = $this->prepare($dto);
        $role = Role::find($dto['role_id']);

        if (!$role) {
            throw new \Exception("Role not found");
        }

        $role->name = $dto['name'] ?? $role->name;
        $role->code = $dto['code'] ?? $role->code;
        $role->description = $dto['description'] ?? $role->description;

        $this->prepareAuditUpdate($role);
        $role->save();

        $this->results['data'] = $role;
        $this->results['message'] = "Role successfully updated";
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
            'role_uuid' => ['required', 'uuid', new ExistsUuid('roles')],
            'name' => ['required'],
            'code' => ['nullable'],
            'description' => ['nullable']
        ];
    }
}
