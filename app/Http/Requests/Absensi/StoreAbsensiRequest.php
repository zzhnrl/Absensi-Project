<?php

namespace App\Http\Requests\Absensi;

use App\Helpers\FormRequest;
use App\Traits\Identifier;

class StoreAbsensiRequest extends FormRequest
{
    use Identifier;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return have_permission('absensi_create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'kategori' => ['required'],
        ];
    }
}
