<?php

namespace App\Http\Requests\StatusCuti;

use App\Helpers\FormRequest;
use App\Traits\Identifier;

class StoreStatusCutiRequest extends FormRequest
{
    use Identifier;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return have_permission('status_cuti_create');
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
