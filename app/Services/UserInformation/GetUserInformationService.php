<?php

namespace App\Services\UserInformation;

use App\Models\UserInformation;
use App\Models\User;
use App\Models\FileStorage;
use App\Services\DefaultService;
use App\Services\ServiceInterface;
use App\Rules\ExistsUuid;

class GetUserInformationService extends DefaultService implements ServiceInterface
{
    public function process($dto)
    {
        $dto['per_page'] = $dto['per_page'] ?? 10;
        $dto['page'] = $dto['page'] ?? 1;
        $dto['sort_by'] = $dto['sort_by'] ?? 'updated_at';
        $dto['sort_type'] = $dto['sort_type'] ?? 'desc';

        $model = UserInformation::where('deleted_at', null)
            ->orderBy($dto['sort_by'], $dto['sort_type']);

        if (isset($dto['search_param']) and $dto['search_param'] != null) {
            $model->where(function ($q) use ($dto) {
                $q->where('nama', 'ILIKE', '%' . $dto['search_param'] . '%');
                $q->where('notlp', 'ILIKE', '%' . $dto['search_param'] . '%');
                $q->where('alamat', 'ILIKE', '%' . $dto['search_param'] . '%');
            });
        }

        if (isset($dto['user_id'])) {
            $model->where('user_id', $dto['user_id']);
        }

        if (isset($dto['signature_file_uuid']) and $dto['signature_file_uuid'] != '') {
            $signature_file_id = $this->findIdByUuid(FileStorage::query(), $dto['signature_file_uuid']);
            $model->where('signature_file_id', $signature_file_id);
        }

        if (isset($dto['user_uuid']) and $dto['user_uuid'] != '') {
            $user_id = $this->findIdByUuid(User::query(), $dto['user_uuid']);
            $model->where('user_id', $user_id);
        }

        if (isset($dto['user_information_uuid']) and $dto['user_information_uuid'] != '') {
            $model->where('uuid', $dto['user_information_uuid']);
            $data = $model->first();
        } else {
            if (isset($dto['with_pagination'])) {
                $this->results['pagination'] = $this->paginationDetail($dto['per_page'], $dto['page'], $model->count());
                $model = $this->paginateData($model, $dto['per_page'], $dto['page']);
            }
            $data = $model->get();
        }

        $this->results['message'] = "User Information successfully fetched";
        $this->results['data'] = $data;
    }

    public function rules($dto)
    {
        return [
            'userinformation_uuid' => ['nullable', 'uuid', new ExistsUuid('user_informations')]
        ];
    }
}
