<?php

namespace App\Services\User;

namespace App\Services;

use App\Models\User;
use App\Rules\ExistsUuid;
use App\Rules\UniqueData;
use App\Services\DefaultService;
use App\Services\ServiceInterface;

class EditUserService extends DefaultService implements ServiceInterface
{
    public function process($dto)
    {
        $dto = $this->prepare($dto);

        // return $this->results['data'] = $dto;

        $updateUserResult = app('UpdateUserService')->execute([
            'user_uuid' => $dto['user_uuid'],
            'photo_uuid' => $dto['photo_uuid'] ?? null,
            'email' => $dto['email'],
            'password' => $dto['password'],
            'role_uuid' => $dto['role_uuid'],
            'remove_picture' => $dto['remove_picture'],
        ], true);

        if (!$updateUserResult['data']) {
            throw new \Exception("Failed to update user data");
        }

        $updateUserInformationResult = app('UpdateUserInformationService')->execute([
            'user_uuid' => $dto['user_uuid'],
            'user_information_uuid' => $dto['user_information_uuid'],
            'signature_file_uuid' => $dto['signature_file_uuid'] ?? null,
            'nama' => $dto['nama'],
            'notlp' => $dto['notlp'],
            'alamat' => $dto['alamat'],
        ], true);

        if (!$updateUserInformationResult['data']) {
            throw new \Exception("Failed to update user information data");
        }

        $this->results['data'] = [
            'user' => $updateUserResult['data'],
            'user_information' => $updateUserInformationResult['data'],
        ];
        $this->results['message'] = "User and user information successfully updated";
    }

    public function prepare($dto)
    {
        $dto['user'] = User::where('uuid', $dto['user_uuid'])->first();
        return $dto;
    }

    public function rules($dto)
    {
        return [
            'user_uuid' => ['required', 'uuid', new ExistsUuid('users')],
            'user_information_uuid' => ['required', 'uuid', new ExistsUuid('user_informations')],
            'email' => ['nullable', 'string', 'email', new UniqueData('users', 'email')],
            'password' => ['nullable', 'confirmed'],
            // 'photo_uuid' => ['nullable', 'uuid', new ExistsUuid('file_storages')],
            'role_uuid' => ['nullable', 'uuid', new ExistsUuid('roles')],
            'nama' => ['nullable', 'string'],
            'notlp' => ['nullable', 'string', new UniqueData('user_informations', 'notlp')],
            'alamat' => ['nullable', 'string'],
            'remove_picture' => ['nullable', 'boolean'],
        ];
    }
}
