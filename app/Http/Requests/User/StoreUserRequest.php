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
            'signature_file' => ['required'],
            'email' => ['required'],
            'password' => ['required'],
            'password_confirmation' => ['required'],
            'role' => ['required'],
            'nomor_telepon' => ['required'],
            'alamat' => ['required'],
            'nama' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'signature_file.required' => "Wajib diisi",
            'email.required' => "Wajib diisi",
            'password.required' => "Wajib diisi",
            'Password_confirmation.required' => "Wajib diisi",
            'role.required' => "Wajib diisi",
            'nomor_telepon' => "Wajib diisi",
            'alamat.required' => "Wajib diisi",
            'nama.required' => "Wajib diisi",
        ];
    }
}
