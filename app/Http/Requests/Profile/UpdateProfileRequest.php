<?php

namespace App\Http\Requests\Profile;

use App\Helpers\FormRequest;
use App\Rules\UniqueData;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $pegawai_uuid = auth()->user()->pegawai->uuid;
        return [
            'nama' => ['required','min:2'],
            'image' => ['nullable', 'mimes:jpg,jpeg,png','max:2000'],
            'email' => ['required','email', new UniqueData('mstr_pegawai','email', $pegawai_uuid)],
            'nomor_telepon' => ['required', new UniqueData('mstr_pegawai','nomor_telepon', $pegawai_uuid)],
            'old_password' => ['nullable'],
            'new_password' => ['required_with:old_password','confirmed'],
        ];
    }
}
