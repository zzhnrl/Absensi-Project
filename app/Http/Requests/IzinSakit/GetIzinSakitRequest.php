<?php

namespace App\Http\Requests\IzinSakit;

use App\Helpers\FormRequest;
use App\Traits\Identifier;
use Illuminate\Http\Exceptions\HttpResponseException;

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

    protected function failedAuthorization()
    {
        throw new HttpResponseException(
            response()->json([
                'data'            => [],
                'recordsTotal'    => 0,
                'recordsFiltered' => 0,
                'error'           => 'Forbidden',
            ], 403)
        );
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
