<?php

namespace App\Http\Requests\Api\FileStorage;

use App\Helpers\FormRequestApi;

class UploadImageRequest extends FormRequestApi
{
    /**
    * Determine if the user is authorized to make this request.
    *
    * @return bool
    */
    public function authorize()
    {
        return have_permission('file_upload');
    }

    /**
    * Get the validation rules that apply to the request.
    *
    * @return array
    */
    public function rules()
    {
        return [
            'image' => ['required','file', 'max:20000', 'mimes:jpeg,jpg,png']
        ];
    }

}
