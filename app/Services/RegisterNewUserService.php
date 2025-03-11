<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use App\Rules\ExistsUuid;

class RegisterNewUserService extends DefaultService implements ServiceInterface
{
    public function process($dto)
    {
        $dto = $this->prepare($dto);
        $user = User::where('deleted_at',null)->where('email', $dto['email'])->first();

        if ($user) {
            throw new \Exception("Email already exists");
        } else {
            $storedUser = app('StoreUserService')->execute([
                "photo_uuid" => $dto['photo_uuid'],
                "email" => $dto['email'],
                "password" => $dto['password'],
                "role_uuid" => $dto['role_uuid']
            ], true);

            app('StoreUserInformationService')->execute([
                "user_uuid" => $storedUser['data']->uuid,
                "signature_file_uuid" => $dto['signature_file_uuid'],
                "nama" => $dto['nama'],
                "notlp" => $dto['notlp'],
                "alamat" => $dto['alamat'],
            ], true);

            $this->results['data'] = $storedUser['data'];
            $this->results['message'] = "User successfully stored";
        }
    }

    public function prepare($dto)
    {
        if (isset($dto['role_uuid'])) {
            $dto['role_id'] = $this->findIdByUuid(Role::query(), $dto['role_uuid']);
        }
        return $dto;
    }

    public function rules($dto)
    {
        return [
            'role_uuid' => ['required', 'uuid', new ExistsUuid('roles')],
            'signature_file_uuid' => ['required', 'uuid', new ExistsUuid('file_storages')],
            'email' => ['required'],
            'password' => ['required'],
            'nama' => ['required'],
            'notlp' => ['required'],
            'alamat' => ['required'],
        ];
    }
}
