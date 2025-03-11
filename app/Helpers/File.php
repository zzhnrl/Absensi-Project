<?php
namespace App\Helpers;

use App\Models\FileStorage;
use Illuminate\Support\Facades\Storage;

class File {

    public static function uploadFile ($dto) {

        $file      = $dto['file'];
        $file_extension = $file->getClientOriginalExtension();
        $file_name  = '';
        do {
            $file_name = Generate::generateRandomString(25);
            $check = FileStorage::where('name',$file_name)->first();
        } while (!empty($check));
        $file_size = $file->getSize();

        $stored = Storage::disk($dto['filesystem'])->put(
            $dto['location'].'/'.$file_name .'.'.$file_extension, file_get_contents($file->getRealPath())
        );

        return [
            "file_name" => $file_name,
            "file_extension" => $file_extension,
            "file_size" => $file_size,
            "location" => $dto['location'],
            "stored" => $stored,
            "filesystem" => $dto['filesystem']
        ];
    }

    public static function compressBeforeuploadFile ($dto) {
        $file      = $dto['file'];
        $file_extension = $file->getClientOriginalExtension();
        $file_name  = '';

        do {
            $file_name = Generate::generateRandomString(25);
            $check = FileStorage::where('name',$file_name)->first();
        } while (!empty($check));

        $file_size = $file->getSize();
        $image = \Image::make($file)->resize(2000,2000, function ($constraint) {
            $constraint->aspectRatio();
        })->encode('jpg');
        $stored = Storage::disk($dto['filesystem'])->put($dto['location'].'/'.$file_name .'.'.$file_extension, (string)$image);

        return [
            "file_name" => $file_name,
            "file_extension" => $file_extension,
            "file_size" => $file_size,
            "location" => $dto['location'],
            "stored" => $stored,
            "filesystem" => $dto['filesystem']
        ];
    }


}
