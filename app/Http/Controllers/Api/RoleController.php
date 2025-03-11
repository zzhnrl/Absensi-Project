<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Role\GetRoleRequest;
use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    public function get(GetRoleRequest $req)
    {
        $req->merge([
            'role_uuid' => $req->role_uuid
        ]);

        $input_dto = $req->all();

        $role = app('GetRoleService')->execute($input_dto);

        return response()->json([
            'success' => (isset($role['error']) ? false : true),
            'message' => $role['message'],
            'data' => $role['data'],
            'pagination' => $role['pagination'] ?? null
        ], $role['response_code']);
    }
}
