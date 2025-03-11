<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FileStorage\UploadImageRequest;

class FileStorageController extends Controller
{
    public function uploadImage(UploadImageRequest $req)
    {
        $dto = [
            'file' => $req->image,
            'location' => ($req->category ?? 'image') . '/'. now()->format('Y-m-d'),
            'filesystem' => $req->filesystem ?? 'public',
            'compress' =>true
        ];

        $file_storage = app('StoreFileStorage')->execute($dto);
        return response()->json([
            'success' => ( isset($file_storage['error']) ? false : true ),
            'message' => $file_storage['message'],
            'data' => $file_storage['data'],
        ], $file_storage['response_code']);
    }
}
