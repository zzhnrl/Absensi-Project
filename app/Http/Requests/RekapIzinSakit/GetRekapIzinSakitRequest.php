<?php

namespace App\Http\Requests\RekapIzinSakit;

use App\Helpers\FormRequest;
use App\Traits\Identifier;

class GetRekapIzinSakitRequest extends FormRequest
{
    use Identifier;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return have_permission('rekap_izin_sakit_view');
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
