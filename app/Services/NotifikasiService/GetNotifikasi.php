<?php
namespace App\Services\NotifikasiService;

use App\Models\Notifikasi;
use App\Models\User;
use App\Rules\ExistsUuid;
use App\Services\DefaultService;
use App\Services\ServiceInterface;

class GetNotifikasi extends DefaultService implements ServiceInterface {

    public function process($dto)
    {
        $dto['per_page'] = $dto['per_page'] ?? 10;
        $dto['page'] = $dto['page'] ?? 1;
        $dto['sort_by'] = $dto['sort_by'] ?? 'created_at';
        $dto['sort_type'] = $dto['sort_type'] ?? 'desc';

        $model = Notifikasi::where('deleted_at',null)->with('user');
        $model->orderBy($dto['sort_by'],$dto['sort_type']);
        if (isset($dto['user_uuid'])) {
            $user_id = $this->findIdByUuid(User::query(), $dto['user_uuid']);
            $model->where('mstr_user_id', $user_id);
        }

        if (isset($dto['is_read'])) {
            $model->where('is_read', $dto['is_read']);
        }

        if (isset($dto['notifikasi_uuid']) and $dto['notifikasi_uuid'] != '') {
            $model->where('uuid', $dto['notifikasi_uuid']);
            $data = $model->first();
        } else {
            if (isset($dto['with_pagination'])) {
                $this->results['pagination'] = $this->paginationDetail($dto['per_page'], $dto['page'], $model->count());
                $model = $this->paginateData($model, $dto['per_page'], $dto['page']);
            }
            $data = $model->get();
        }

        $this->results['message'] = "Notifikasi successfully fetched";
        $this->results['data'] = $data;
    }

    public function rules ($dto) {
        return [
            'notifikasi_uuid' => ['nullable', 'uuid', new ExistsUuid('notifikasi')]
        ];
    }

}
