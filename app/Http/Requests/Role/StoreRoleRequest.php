<?php

namespace App\Http\Requests\Role;

use App\Helpers\FormRequest;
use App\Rules\ExistsUuid;
use App\Rules\UniqueData;
use Illuminate\Validation\Rules\Exists;

class StoreRoleRequest extends FormRequest
{
    /**
     * Determine if the Role is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return have_permission('role_create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required'],
            'code' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => "Wajib diisi",
            'code.required' => "Wajib diisi",
        ];
    }
}
