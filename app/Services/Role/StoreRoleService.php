<?php

namespace App\Services\Role;

use App\Models\Role;
use App\Rules\UniqueData;
use App\Services\DefaultService;
use App\Services\ServiceInterface;

class StoreRoleService extends DefaultService implements ServiceInterface
{

    public function process($dto)
    {
        $dto = $this->prepare($dto);
        $role = new Role;

        $role->name = $dto['name'];
        $role->code = $dto['code'] ?? null;
        $role->description = $dto['description'] ?? null;

        $this->prepareAuditActive($role);
        $this->prepareAuditInsert($role);
        $role->save();

        $this->results['data'] = $role;
        $this->results['message'] = "Role berhasil ditambahkan.";
    }

    public function prepare($dto)
    {
        return $dto;
    }

    public function rules($dto)
    {
        return [
            'name' => ['required', new UniqueData('roles', 'name')],
            'code' => ['nullable', new UniqueData('roles', 'code')],
            'description' => ['nullable']
        ];
    }
}
