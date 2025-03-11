<?php

namespace App\Http\Requests\IzinSakit;

use App\Helpers\FormRequest;
use App\Rules\ExistsUuid;

class DeleteIzinSakitRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return have_permission('izin_sakit_delete');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_uuid' => ['required', 'uuid', new ExistsUuid('users')],
            'userinformation_uuid' => ['required', 'uuid', new ExistsUuid('user_informations')]
        ];
    }
}
