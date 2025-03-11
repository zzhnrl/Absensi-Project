<?php

namespace App\Http\Requests\User;

use App\Helpers\FormRequest;
use App\Rules\ExistsUuid;
use App\Rules\UniqueData;
use Illuminate\Validation\Rules\Exists;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return have_permission('user_create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'nama' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'nama.required' => "Nama tidak boleh kosong",
        ];
    }
}
