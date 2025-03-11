<?php

namespace App\Services\Permission;

use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;
use App\Services\DefaultService;
use App\Services\ServiceInterface;
use Illuminate\Support\Facades\DB;

class UpdateRolePermission extends DefaultService implements ServiceInterface
{

    public function process($dto)
    {
        $role_id = $this->findIdByUuid(Role::query(), $dto['role_uuid']);
        $permission_id = Permission::where('uuid', $dto['permission_uuid'])->first()['id'];

        $role_permission = RolePermission::where('role_id', $role_id)->where('permission_id', $permission_id)->first();

        if ($role_permission != null) {
            RolePermission::where('role_id', $role_id)->where('permission_id', $permission_id)->delete();
        } else {
            RolePermission::insert([
                'role_id' => $role_id,
                'permission_id' => $permission_id,
            ]);
        }
        $this->results['message'] = "Role Permission berhasil di update.";
        $this->results['data'] = true;
    }
}
