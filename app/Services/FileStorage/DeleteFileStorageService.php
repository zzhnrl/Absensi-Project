<?php
namespace App\Services\FileStorage;

use App\Exceptions\CustomException;
use App\Helpers\Generate;
use App\Models\User;
use App\Services\DefaultService;
use App\Services\ServiceInterface;
use App\Models\FileStorage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class DeleteFileStorageService extends DefaultService implements ServiceInterface {

    public function process($dto)
    {
        $dto = $this->prepare($dto);
        $file_storage = FileStorage::find($dto['file_storage_id']);

        Storage::disk('s3')->delete("file_storage/".$file_storage['name'].".".$file_storage['extension']);
        $this->results['message'] = "File storage successfully deleted";
        $this->results['data'] = $this->activeAndRemoveData($file_storage, $dto);

    }


    private function prepare ($dto) {

        if (!isset($dto['file_storage_uuid'])) throw new CustomException('file storage uuid required', 400);
        $file_storage_id = $this->findIdByUuid(FileStorage::query(), $dto['file_storage_uuid']);
        if ( $file_storage_id == null) throw new CustomException ('file storage not found', 400);
        return [
            'file_storage_id' => $file_storage_id
        ];
    }

}



