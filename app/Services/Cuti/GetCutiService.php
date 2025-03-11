<?php

namespace App\Services\Cuti;

use App\Models\Cuti;
use App\Models\StatusCuti;
use App\Models\User;
use App\Services\DefaultService;
use App\Services\ServiceInterface;
use App\Rules\ExistsUuid;
use Carbon\Carbon;

class GetCutiService extends DefaultService implements ServiceInterface
{
    public function process($dto)
    {
        $dto['per_page'] = $dto['per_page'] ?? 10;
        $dto['page'] = $dto['page'] ?? 1;
        $dto['sort_by'] = $dto['sort_by'] ?? 'updated_at';
        $dto['sort_type'] = $dto['sort_type'] ?? 'desc';

        $model = Cuti::where('deleted_at', null)
            ->orderBy($dto['sort_by'], $dto['sort_type'])
            ->with('statusCuti','user.userInformation.signatureFile');


        if (isset($dto['search_param']) and $dto['search_param'] != null) {
            $model->where(function ($q) use ($dto) {
                $q->where('nama', 'ILIKE', '%' . $dto['search_param'] . '%');
            });
        }

        if (isset($dto['user_id_in'])) {
            $model->whereIn('user_id', $dto['user_id_in']);
        }
        
        if (isset($dto['user_uuid']) and $dto['user_uuid'] != '') {
            $user_id = $this->findIdByUuid(User::query(), $dto['user_uuid']);
            $model->where('user_id', $user_id);
        }

        if (isset($dto['status_cuti_uuid']) and $dto['status_cuti_uuid'] != '') {
            $status_cuti_id = $this->findIdByUuid(StatusCuti::query(), $dto['status_cuti_uuid']);
            $model->where('status_cuti_id', $status_cuti_id);
        }

        if (isset($dto['date_range'])) {
            $date = explode(' to ', $dto['date_range']);

            if (!isset($date[1])) {
                $model->where(function ($q) use ($dto,$date) {
                    $date1 = Carbon::parse($date[0])->format('Y-m-d');
                    $q->whereDate('tanggal_mulai',  $date1);
                });
            }else  {
                $model->where(function ($q) use ($dto,$date) {
                    $date1 = Carbon::parse($date[0])->format('Y-m-d');
                    $q->whereDate('tanggal_mulai','>=',  $date1);
                });
                $model->where(function ($q) use ($dto,$date) {
                    $date2 = Carbon::parse($date[1])->format('Y-m-d');
                    $q->whereDate('tanggal_mulai','<=',  $date2);
                });
            }
        }

        if (isset($dto['cuti_uuid']) and $dto['cuti_uuid'] != '') {
            $model->where('uuid', $dto['cuti_uuid']);
            $data = $model->first();
        } else {
            if (isset($dto['with_pagination'])) {
                $this->results['pagination'] = $this->paginationDetail($dto['per_page'], $dto['page'], $model->count());
                $model = $this->paginateData($model, $dto['per_page'], $dto['page']);
            }
            $data = $model->get();
        }

        $this->results['message'] = "Cuti successfully fetched";
        $this->results['data'] = $data;
    }

    public function rules($dto)
    {
        return [
            'cuti_uuid' => ['nullable', 'uuid', new ExistsUuid('cutis')]
        ];
    }
}
