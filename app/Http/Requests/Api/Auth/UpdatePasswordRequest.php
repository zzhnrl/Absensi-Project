<?php

namespace App\Http\Requests\Api\Auth;

use App\Helpers\FormRequestApi;
use App\Rules\ExistsUuid;
use App\Rules\UniqueData;

class UpdatePasswordRequest extends FormRequestApi
{
    /**
    * Determine if the user is authorized to make this request.
    *
    * @return bool
    */
    public function authorize()
    {
        return have_permission('password_update');
    }

    /**
    * Get the validation rules that apply to the request.
    *
    * @return array
    */
    public function rules()
    {
        return [
            'old_password' => ['required'],
            'new_password' => ['required', 'confirmed'],
        ];
    }

}
