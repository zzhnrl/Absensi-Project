<?php

namespace App\Http\Requests\User;

use App\Helpers\FormRequest;
use App\Rules\ExistsUuid;
use App\Rules\UniqueData;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return have_permission('user_edit');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // $user_uuid = request()->route()->parameter('user');
        return [
            'email' => ['required', 'min:2'],
            'role' => ['required', new ExistsUuid('roles')],
            'nama' => ['required'],
            'notlp' => ['required'],
            'alamat' => ['required'],
        ];
    }
}
