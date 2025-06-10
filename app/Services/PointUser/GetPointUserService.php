<?php

namespace App\Services\PointUser;

use App\Models\PointUser;
use App\Models\User;
use App\Services\DefaultService;
use App\Services\ServiceInterface;
use App\Rules\ExistsUuid;

class GetPointUserService extends DefaultService implements ServiceInterface
{

    public function process($dto)
    {
        $dto['per_page'] = $dto['per_page'] ?? 10;
        $dto['page'] = $dto['page'] ?? 1;
        $dto['sort_by'] = $dto['sort_by'] ?? 'updated_at';
        $dto['sort_type'] = $dto['sort_type'] ?? 'desc';

        $model = PointUser::where('deleted_at', null)
            ->orderBy($dto['sort_by'], $dto['sort_type'])
            ->with('user');

        if (isset($dto['search_param']) and $dto['search_param'] != null) {
            $model->where(function ($q) use ($dto) {
                $q->where('bulan', 'ILIKE', '%' . $dto['search_param'] . '%')
                ->orwhere('nama_karyawan', 'ILIKE', '%' . $dto['search_param'] . '%');
            });
        }

        if (isset($dto['user_id_in'])) {
            $model->whereIn('user_id', $dto['user_id_in']);
        }

        if (isset($dto['month'])) {
            $model->where('bulan', $dto['month']);
        }

        if (isset($dto['year'])) {
            $model->where('tahun', $dto['year']);
        }

        if (isset($dto['user_uuid']) and $dto['user_uuid'] != '') {
            $user_id = $this->findIdByUuid(User::query(), $dto['user_uuid']);
            $model->where('user_id', $user_id);
        }

        if (isset($dto['point_user_uuid']) and $dto['point_user_uuid'] != '') {
            $model->where('uuid', $dto['point_user_uuid']);
            $data = $model->first();
        } else {
            if (isset($dto['with_pagination'])) {
                $this->results['pagination'] = $this->paginationDetail($dto['per_page'], $dto['page'], $model->count());
                $model = $this->paginateData($model, $dto['per_page'], $dto['page']);
            }

        $data = $model;
        }

        $this->results['message'] = "Point user successfully fetched";
        $this->results['data'] = $data;
    }

    public function rules($dto)
    {
        return [
            'point_user_uuid' => ['nullable', 'uuid', new ExistsUuid('point_users')]
        ];
    }
}
