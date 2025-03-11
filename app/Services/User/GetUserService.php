<?php

namespace App\Services\User;

use App\Models\Role;
use App\Models\User;
use App\Models\FileStorage;
use App\Services\DefaultService;
use App\Services\ServiceInterface;
use App\Rules\ExistsUuid;

class GetUserService extends DefaultService implements ServiceInterface
{
    public function process($dto)
    {
        $dto['per_page'] = $dto['per_page'] ?? 10;
        $dto['page'] = $dto['page'] ?? 1;
        $dto['sort_by'] = $dto['sort_by'] ?? 'updated_at';
        $dto['sort_type'] = $dto['sort_type'] ?? 'desc';

        $model = User::where('deleted_at', null)
            ->orderBy($dto['sort_by'], $dto['sort_type'])
            ->with('userRole.role', 'photo', 'userInformation');

        if (isset($dto['search_param']) and $dto['search_param'] != null) {
            $model->where(function ($q) use ($dto) {
                $q->where('email', 'ILIKE', '%' . $dto['search_param'] . '%');
            });
        }

        if (isset($dto['role_id_not_in'])) {
            $role_id = $dto['role_id_not_in'];
            // $role_id = $this->findIdByUuid(Role::query(), $dto['role_id_not_in']);
            $model->whereHas('userRole', function ($data) use ($role_id) {
                $data->whereNotIn('role_id', $role_id);
            });
        }

        if (isset($dto['role_uuid']) and $dto['role_uuid'] != '') {
            $role_id = $this->findIdByUuid(Role::query(), $dto['role_uuid']);
            $model->whereHas('userRole', function ($item) use ($role_id) {
                $item->where('role_id', $role_id);
            });
        }

        if (isset($dto['photo_uuid']) and $dto['photo_uuid'] != '') {
            $photo_id = $this->findIdByUuid(FileStorage::query(), $dto['photo_uuid']);
            $model->where('photo_id', $photo_id);
        }

        if (isset($dto['signature_file_uuid']) and $dto['signature_file_uuid'] != '') {
            $signature_file_id = $this->findIdByUuid(FileStorage::query(), $dto['signature_file_uuid']);
            $model->where('signature_file_id', $signature_file_id);
        }

        if (isset($dto['user_uuid']) and $dto['user_uuid'] != '') {
            $model->where('uuid', $dto['user_uuid']);
            $data = $model->first();
        } else {
            if (isset($dto['with_pagination'])) {
                $this->results['pagination'] = $this->paginationDetail($dto['per_page'], $dto['page'], $model->count());
                $model = $this->paginateData($model, $dto['per_page'], $dto['page']);
            }
            $data = $model->get();
        }

        $this->results['message'] = "User successfully fetched";
        $this->results['data'] = $data;
    }

    public function rules($dto)
    {
        return [
            'user_uuid' => ['nullable', 'uuid', new ExistsUuid('users')]
        ];
    }
}
