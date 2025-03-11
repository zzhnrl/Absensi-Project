<?php

namespace App\Services\Permission;


use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;
use App\Services\DefaultService;
use App\Services\ServiceInterface;
use Illuminate\Support\Facades\DB;

class GetListPermissionModule extends DefaultService implements ServiceInterface
{

    public function process($dto)
    {

        $input_dto['role_id'] = $this->findIdByUuid(Role::query(), $dto['role_uuid']);
        $data = DB::select('SELECT module_name FROM permissions group by module_name order by module_name asc');
        $data = datatables()
            ->of($data)
            ->rawColumns(['action', 'permission', 'access'])
            ->addColumn('permission', function ($row) {
                return ' <label class="text-nowrap fw-bolder">' . $row->module_name . '</label>';
            })
            ->addColumn('access', function ($row) use ($input_dto) {
                $string = "<div class='d-flex'>";

                $permission = Permission::where('module_name', $row->module_name)->get();

                foreach ($permission as $row) {

                    $role_have_permission = RolePermission::where(
                        'role_id',
                        $input_dto['role_id']
                    )->where('permission_id', $row->id)->count();

                    $string .=  "<div class='form-check m-2'>";
                    $string .= "<input class='form-check-input' type='checkbox' id='$row->uuid' " . ($role_have_permission > 0 ? "checked" : "") . "/>";
                    $string .= "<label class='form-check-label' > $row->permission_name </label>";
                    $string .= "</div>";
                }

                $string .= "</div>";
                return $string;
            })
            ->toJson();

        $this->results['message'] = "Permission successfully fetched";
        $this->results['data'] = $data;
    }
}
