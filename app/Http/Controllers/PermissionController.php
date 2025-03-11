<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($role_uuid)
    {
        if (have_permission('user_edit')) {
            $role = app('GetRoleService')->execute([
                'role_uuid' => $role_uuid
            ]);
            if (empty($role['data']))
                return view('errors.404');

            $breadcrumb = [
                ['link' => '/', 'name' => 'Dashboard'],
                ['link' => '/role', 'name' => 'Role'],
                ['link' => "/role/$role_uuid/permission", 'name' => 'Permission']
            ];
            return view('role.permission.index', [
                'breadcrumb' => breadcrumb($breadcrumb),
                'role' => $role['data']
            ]);
        }
        return view('errors.403');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateRole(Request $request, $role_uuid)
    {
        if (have_permission('assign_permission_to_role_add_remove_permission')) {
            try {
                DB::beginTransaction();
                $request->merge($request->json()->all());
                $data = app('UpdateRolePermission')->execute([
                    'role_uuid' => $role_uuid,
                    'permission_uuid' => $request->permission_uuid
                ]);

                DB::commit();
                return response()->json([
                    "success" => true,
                    "message" => $data['message']
                ], 200);
            } catch (\Exception $e) {
                DB::rollback();
                return  response()->json([
                    "success" => false,
                    "message" => 'System Failure',
                    "errors" => $e->getMessage()
                ], 500);
            }
        }
        return  response()->json([
            "success" => false,
        ], 403);
    }


    /**
     * Display a listing of the resource in datatable formats.
     *
     * @return \Illuminate\Http\Response
     */
    public function grid(Request $request, $role_uuid)
    {
        return app('GetListPermissionModule')->execute([
            'role_uuid' => $role_uuid,
        ])['data'];
    }
}
