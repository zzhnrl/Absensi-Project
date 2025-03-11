<?php
namespace App\Services\FileStorage;

use App\Exceptions\CustomException;
use App\Services\DefaultService;
use App\Services\ServiceInterface;
use App\Helpers\File;
use App\Models\FileStorage;

class StoreFileStorageService extends DefaultService implements ServiceInterface {

    public function process ($dto) {

        $file = ($dto['compress']) ? File::compressBeforeuploadFile($dto) : File::uploadFile($dto);
        $dto = $this->prepare($file);
        $file_storage = new FileStorage;

        $file_storage->size = $dto['size'] ?? null;
        $file_storage->extension = $dto['extension'] ?? null;
        $file_storage->name = $dto['name'] ?? null;
        $file_storage->location = $dto['location'] ?? null;
        $file_storage->remark = $dto['remark'] ?? null;
        $file_storage->filesystem = $dto['filesystem'] ?? null;

        $this->prepareAuditActive($file_storage);
        $this->prepareAuditInsert($file_storage);
        $file_storage->save();

        $this->results['data'] = $file_storage;
        $this->results['message'] = "File successfully stored";

    }

    public function rules ($dto) {
        return [
            'file' => ['required','file']
        ];
    }

    private function prepare($dto) {

        return [
            'size' => $dto['file_size'],
            'extension' => $dto['file_extension'],
            'name' => $dto['file_name'],
            'location' => $dto['location'],
            'filesystem' => $dto['filesystem'],
            'remark' => '',
            'segment' => ''
        ];

    }


}
