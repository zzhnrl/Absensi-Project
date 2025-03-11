<?php

namespace App\Http\Requests\IzinSakit;

use App\Helpers\FormRequest;
use App\Rules\ExistsUuid;
use App\Rules\UniqueData;
use Illuminate\Validation\Rules\Exists;

class StoreIzinSakitRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return have_permission('izin_sakit_create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // 'user_uuid' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            // 'user_uuid.required' => "Nama tidak boleh kosong",
        ];
    }
}
