<?php

namespace App\Http\Requests\IzinSakit;

use App\Helpers\FormRequest;
use App\Traits\Identifier;

class GetIzinSakitRequest extends FormRequest
{
    use Identifier;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return have_permission('izin_sakit_view');
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
