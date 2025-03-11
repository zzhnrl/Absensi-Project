<?php

namespace App\Http\Requests\StatusCuti;

use App\Helpers\FormRequest;
use App\Rules\ExistsUuid;
use App\Rules\UniqueData;

class UpdateStatusCutiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return have_permission('status_cuti_edit');
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
            'description' => ['nullable'],
        ];
    }
}
