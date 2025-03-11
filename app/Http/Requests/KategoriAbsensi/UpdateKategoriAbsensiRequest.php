<?php

namespace App\Http\Requests\KategoriAbsensi;

use App\Helpers\FormRequest;
use App\Rules\ExistsUuid;
use App\Rules\UniqueData;

class UpdateKategoriAbsensiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return have_permission('kategori_absensi_edit');
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
            'name' => ['required'],
            'code' => ['required'],
            'point' => ['required'],
            'description' => ['nullable'],
        ];
    }
}
