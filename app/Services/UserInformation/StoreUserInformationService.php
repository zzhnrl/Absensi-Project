<?php

namespace App\Services\UserInformation;

use App\Models\UserInformation;
use App\Models\FileStorage;
use App\Services\DefaultService;
use App\Services\ServiceInterface;
use App\Models\User;
use App\Rules\UniqueData;
use App\Rules\ExistsUuid;

class StoreUserInformationService extends DefaultService implements ServiceInterface
{
    public function process($dto)
    {
        $dto = $this->prepare($dto);
        $userinformation = new UserInformation();

        if (isset($dto['signature_file_id'])) {
            FileStorage::find($dto['signature_file_id'])->update([
                "is_used" => true
            ]);
        }

        $userinformation->signature_file_id = $dto['signature_file_id']?? null;
        $userinformation->nama = $dto['nama'];
        $userinformation->notlp = $dto['notlp'];
        $userinformation->alamat = $dto['alamat'];
        $userinformation->user_id = $dto['user_id'];

        $this->prepareAuditActive($userinformation);
        $this->prepareAuditInsert($userinformation);
        $userinformation->save();

        $this->results['data'] = $userinformation;
        $this->results['message'] = "User Information successfully created";
    }

    public function prepare($dto)
    {
        if (isset($dto['user_uuid'])) {
            $dto['user_id'] = $this->findIdByUuid(User::query(), $dto['user_uuid']);
        }

        if (isset($dto['signature_file_uuid'])) {
            $dto['signature_file_id'] = $this->findIdByUuid(FileStorage::query(), $dto['signature_file_uuid']);
        }

        return $dto;
    }

    public function rules($dto)
    {
        return [
            'user_uuid' => ['required', 'uuid', new ExistsUuid('users')],
            // 'signature_file_uuid' => ['nullable', 'uuid', new ExistsUuid('file_storages')],
            'nama' => ['required', 'string', new UniqueData('user_informations', 'nama')],
            'notlp' => ['required', 'string', new UniqueData('user_informations', 'notlp')],
            'alamat' => ['required', 'string', new UniqueData('user_informations', 'alamat')],
        ];
    }
}
