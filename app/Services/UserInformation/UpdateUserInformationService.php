<?php

namespace App\Services\UserInformation;

use App\Services\DefaultService;
use App\Services\ServiceInterface;
use App\Models\UserInformation;
use App\Models\FileStorage;
use App\Rules\ExistsUuid;
use App\Rules\UniqueData;
use App\Models\User;

class UpdateUserInformationService extends DefaultService implements ServiceInterface
{
    public function process($dto)
    {
        $dto = $this->prepare($dto);
        $userinformation = UserInformation::find($dto['user_information_id']);

        if (!$userinformation) {
            throw new \Exception("User information not found");
        }

        if (isset($dto['signature_file_id'])) {
            if (isset($user->signature_file_id)) {
                FileStorage::find($user->signature_file_id)->update([
                    "is_used" => false
                ]);
            }
            FileStorage::find($dto['signature_file_id'])->update([
                "is_used" => true
            ]);
        }

        if (isset($dto['remove_picture'])) {
            FileStorage::find($dto['remove_picture'])->update(['is_used' => false]);
        }

        $userinformation->signature_file_id = (isset($dto['remove_picture'])) ? null : ($dto['signature_file_id'] ?? $userinformation->signature_file_id);
        $userinformation->user_id = $dto['user_id'] ?? $userinformation->user_id;
        $userinformation->nama = $dto['nama'] ?? $userinformation->nama;
        $userinformation->notlp = $dto['notlp'] ?? $userinformation->notlp;
        $userinformation->alamat = $dto['alamat'] ?? $userinformation->alamat;

        $this->prepareAuditUpdate($userinformation);
        $userinformation->save();

        $this->results['data'] = $userinformation;
        $this->results['message'] = "User Information successfully updated";
    }

    public function prepare($dto)
    {
        if (isset($dto['user_information_uuid'])) {
            $dto['user_information_id'] = $this->findIdByUuid(UserInformation::query(), $dto['user_information_uuid']);
        }

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
            'user_information_uuid' => ['required', 'uuid', new ExistsUuid('user_informations')],
            'signature_file_uuid' => ['nullable', 'uuid', new ExistsUuid('file_storages')],
            'nama' => ['required', 'string'],
            'notlp' => ['required', 'string', new UniqueData('user_informations', 'notlp')],
            'alamat' => ['required', 'string'],
        ];
    }
}
