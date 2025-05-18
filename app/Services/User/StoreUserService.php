<?php

namespace App\Services\User;

use App\Models\FileStorage;
use App\Services\DefaultService;
use App\Services\ServiceInterface;
use App\Models\User;
use App\Rules\ExistsUuid;
use App\Rules\UniqueData;
use Illuminate\Support\Facades\Crypt;

class StoreUserService extends DefaultService implements ServiceInterface
{
    public function process($dto)
    {
        $dto = $this->prepare($dto);
        $user = new User;

        if (isset($dto['photo_id'])) {
            FileStorage::find($dto['photo_id'])->update([
                "is_used" => true
            ]);
        }

        $user->photo_id = $dto['photo_id'] ?? null;
        $user->email = $dto['email'];
        $user->password = $dto['password'];
        $user->sisa_cuti = $dto['sisa_cuti'];
        $user->role_id = $dto['role_id'];

        $this->prepareAuditActive($user);
        $this->prepareAuditInsert($user);
        $user->save();

        app('AddUserRoleService')->execute([
            'user_uuid' => $user->uuid,
            'role_uuid' => $dto['role_uuid']
        ], true);

        $this->results['data'] = $user;
        $this->results['message'] = "User successfully created";
    }

    public function prepare($dto)
    {
        if (isset($dto['password'])) {
            $dto['password'] = Crypt::encryptString($dto['password']);
        }
        
        if (isset($dto['photo_uuid'])) {
            $dto['photo_id'] = $this->findIdByUuid(FileStorage::query(), $dto['photo_uuid']);
        }

        return $dto;
    }

    public function rules($dto)
    {
        return [
            'photo_uuid' => ['required', 'uuid', new ExistsUuid('file_storages')],
            'email' => ['required', 'string', new UniqueData('users', 'email')],
            'password' => ['required', 'string', 'min:8'],
        ];
    }
}
