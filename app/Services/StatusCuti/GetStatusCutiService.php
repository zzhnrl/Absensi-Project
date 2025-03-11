<?php

namespace App\Services\StatusCuti;

use App\Models\StatusCuti;
use App\Services\DefaultService;
use App\Services\ServiceInterface;
use App\Rules\ExistsUuid;

class GetStatusCutiService extends DefaultService implements ServiceInterface
{
    public function process($dto)
    {
        $dto['per_page'] = $dto['per_page'] ?? 10;
        $dto['page'] = $dto['page'] ?? 1;
        $dto['sort_by'] = $dto['sort_by'] ?? 'updated_at';
        $dto['sort_type'] = $dto['sort_type'] ?? 'desc';

        $model = StatusCuti::where('deleted_at', null)
            ->orderBy($dto['sort_by'], $dto['sort_type']);

        if (isset($dto['search_param']) and $dto['search_param'] != null) {
            $model->where(function ($q) use ($dto) {
                $q->where('nama', 'ILIKE', '%' . $dto['search_param'] . '%')
                    ->orwhere('kode', 'ILIKE', '%' . $dto['search_param'] . '%')
                    ->orwhere('deskripsi', 'ILIKE', '%' . $dto['search_param'] . '%');
            });
        }

        if (isset($dto['status_cuti_uuid']) and $dto['status_cuti_uuid'] != '') {
            $model->where('uuid', $dto['status_cuti_uuid']);
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
            'status_cuti_uuid' => ['nullable', 'uuid', new ExistsUuid('status_cutis')]
        ];
    }
}
