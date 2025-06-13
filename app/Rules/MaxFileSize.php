<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\FileStorage;

class MaxFileSize implements Rule
{
    protected $maxSizeMB;

    public function __construct($maxSizeMB)
    {
        $this->maxSizeMB = $maxSizeMB;
    }

    public function passes($attribute, $value)
    {
        // Cek apakah UUID valid dan file-nya ditemukan
        $file = FileStorage::where('uuid', $value)->first();
        if (!$file || !$file->size) {
            return false;
        }

        // Hitung ukuran dalam MB
        $fileSizeInMB = $file->size / 1024 / 1024;

        return $fileSizeInMB <= $this->maxSizeMB;
    }

    public function message()
    {
        return "Ukuran file tidak boleh lebih dari {$this->maxSizeMB} MB.";
    }
}
