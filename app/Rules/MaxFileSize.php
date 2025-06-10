<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\FileStorage;

class MaxFileSize implements Rule
{
    protected $maxSize; // bytes
    protected $message;

    public function __construct($maxSizeInMB)
    {
        $this->maxSize = $maxSizeInMB * 1024 * 1024;
        $this->message = "Ukuran file maksimal adalah {$maxSizeInMB} MB.";
    }

    public function passes($attribute, $value)
    {
        $file = FileStorage::where('uuid', $value)->first();
        if (!$file) {
            $this->message = "File tidak ditemukan.";
            return false;
        }

        return $file->size <= $this->maxSize;
    }

    public function message()
    {
        return $this->message;
    }
}
