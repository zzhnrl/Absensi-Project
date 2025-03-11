<?php

namespace App\Http\Requests\Api\RolePermission;

use App\Helpers\FormRequestApi;
use App\Traits\Identifier;

class GetRolePermissionRequest extends FormRequestApi
{
    use Identifier;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return have_permission('role_permission_view');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
