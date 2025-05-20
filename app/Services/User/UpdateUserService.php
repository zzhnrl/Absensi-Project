<?php

namespace App\Services\User;

use App\Models\FileStorage;
use App\Services\DefaultService;
use App\Services\ServiceInterface;
use App\Models\User;
use App\Models\UserInformation;
use App\Rules\ExistsUuid;
use App\Rules\UniqueData;
use Illuminate\Support\Facades\Crypt;

class UpdateUserService extends DefaultService implements ServiceInterface
{
    public function process($dto)
    {
        $dto = $this->prepare($dto);
        $user = User::find($dto['user_id']);

        if (!$user) {
            throw new \Exception("User not found");
        }

        if (isset($dto['photo_id'])) {
            if (isset($user->photo_id)) {
                FileStorage::find($user->photo_id)->update([
                    "is_used" => false
                ]);
            }
            FileStorage::find($dto['photo_id'])->update([
                "is_used" => true
            ]);
        }

        if (isset($dto['remove_picture'])) {
            FileStorage::find($dto['remove_picture'])->update(['is_used' => false]);
        }

        $user->photo_id = (isset($dto['remove_picture'])) ? null : ($dto['photo_id'] ?? $user->photo_id);
        $user->email = $dto['email'] ?? $user->email;
        $user->password = $dto['password'] ?? $user->password;

        if (isset($dto['role_uuid'])) {
            app('RemoveUserRoleService')->execute([
                'user_uuid' => $user->uuid,
                'role_uuid' => $user->userRole->role->uuid
            ], true);
            app('AddUserRoleService')->execute([
                'user_uuid' => $user->uuid,
                'role_uuid' => $dto['role_uuid']
            ], true);
        }

        $this->prepareAuditUpdate($user);
        $user->save();

        $this->results['data'] = $user;
        $this->results['message'] = "User successfully updated";
    }

    public function prepare($dto)
    {
        if (isset($dto['user_uuid'])) {
            $dto['user_id'] = $this->findIdByUuid(User::query(), $dto['user_uuid']);
        }

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
            'user_uuid' => ['required', 'uuid', new ExistsUuid('users')],
            'role_uuid' => ['nullable', 'uuid', new ExistsUuid('roles')],
            'photo_uuid' => ['nullable', 'uuid', new ExistsUuid('file_storages')],
            'email' => ['nullable', 'string', 'email', new UniqueData('users', 'email')],
            'password' => ['nullable', 'confirmed'],
        ];
    }
}
