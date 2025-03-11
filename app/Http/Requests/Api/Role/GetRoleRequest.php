<?php

namespace App\Http\Requests\Api\Role;

use App\Helpers\FormRequest;
use App\Traits\Identifier;

class GetRoleRequest extends FormRequest
{
    use Identifier;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return have_permission('role_view');
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
