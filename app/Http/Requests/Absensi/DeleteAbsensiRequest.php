<?php

namespace App\Http\Requests\Absensi;

use App\Helpers\FormRequest;
use App\Traits\Identifier;
use App\Rules\ExistsUuid;

class DeleteAbsensiRequest extends FormRequest
{
    use Identifier;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return have_permission('absensi_delete');
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
            'userinformation_uuid' => ['required', 'uuid', new ExistsUuid('user_informations')],
            'kategori_uuid' => ['required', 'uuid', new ExistsUuid('katgeori_absensis')]
        ];
    }
}
