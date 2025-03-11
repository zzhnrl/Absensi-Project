<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests\Api\RolePermission\GetRolePermissionRequest;
use App\Http\Requests\Api\RolePermission\UpdateRolePermissionRequest;
use App\Http\Controllers\Controller;

class RolePermissionController extends Controller
{
    public function get (GetRolePermissionRequest $req) {
        $input_dto = [
            'role_uuid' => $req->uuid,
        ];

        $role_permission = app('GetRolePermission')->execute($input_dto);

        return response()->json([
            'success' => ( isset($role_permission['error']) ? false : true ),
            'message' => $role_permission['message'],
            'data' => $role_permission['data'],
        ], $role_permission['response_code']);
    }

    public function update (UpdateRolePermissionRequest $req) {
        $input_dto = [
            'role_uuid' => $req->role_uuid,
            'permission_uuid' => $req->permission_uuid
        ];

        $role_permission = app('UpdateRolePermission')->execute($input_dto);

        return response()->json([
            'success' => ( isset($role_permission['error']) ? false : true ),
            'message' => $role_permission['message'],
            'data' => $role_permission['data'],
        ], $role_permission['response_code']);
    }
}
