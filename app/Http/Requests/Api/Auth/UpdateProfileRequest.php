<?php

namespace App\Http\Requests\Api\Auth;

use App\Helpers\FormRequestApi;
use App\Rules\ExistsUuid;
use App\Rules\UniqueData;

class UpdateProfileRequest extends FormRequestApi
{
    /**
    * Determine if the user is authorized to make this request.
    *
    * @return bool
    */
    public function authorize()
    {
        return have_permission('profile_edit');
    }

    /**
    * Get the validation rules that apply to the request.
    *
    * @return array
    */
    public function rules()
    {
        return [
            'photo_uuid' => ['nullable', 'uuid' , new ExistsUuid('mstr_file_storage')],
            'email' => ['required', 'email' , new UniqueData('mstr_user','phone_number', auth()->user()->uuid)],
            'phone_number' => ['required', new UniqueData('mstr_user','email', auth()->user()->uuid)],
            'name' => ['required', 'min:2'],
            'password' => ['nullable','confirmed','min:6'],
        ];
    }

}
