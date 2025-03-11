<?php

namespace App\Services\IzinSakit;

use App\Models\IzinSakit;
use App\Models\User;
use App\Models\FileStorage;
use App\Services\DefaultService;
use App\Services\ServiceInterface;
use App\Rules\ExistsUuid;
use Carbon\Carbon;

class GetIzinSakitService extends DefaultService implements ServiceInterface
{
    public function process($dto)
    {
        $dto['per_page'] = $dto['per_page'] ?? 10;
        $dto['page'] = $dto['page'] ?? 1;
        $dto['sort_by'] = $dto['sort_by'] ?? 'updated_at';
        $dto['sort_type'] = $dto['sort_type'] ?? 'desc';

        $model = IzinSakit::where('deleted_at', null)
            ->orderBy($dto['sort_by'], $dto['sort_type']);

        if (isset($dto['search_param']) and $dto['search_param'] != null) {
            $model->where(function ($q) use ($dto) {
                $q->where('nama', 'ILIKE', '%' . $dto['search_param'] . '%');
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

        if (isset($dto['photo_uuid']) and $dto['photo_uuid'] != '') {
            $photo_id = $this->findIdByUuid(FileStorage::query(), $dto['photo_uuid']);
            $model->where('photo_id', $photo_id);
        }

        if (isset($dto['date_range'])) {
            $date = explode(' to ', $dto['date_range']);

            if (!isset($date[1])) {
                $model->where(function ($q) use ($dto,$date) {
                    $date1 = Carbon::parse($date[0])->format('Y-m-d');
                    $q->whereDate('tanggal',  $date1);
                });
            }else  {
                $model->where(function ($q) use ($dto,$date) {
                    $date1 = Carbon::parse($date[0])->format('Y-m-d');
                    $q->whereDate('tanggal','>=',  $date1);
                });
                $model->where(function ($q) use ($dto,$date) {
                    $date2 = Carbon::parse($date[1])->format('Y-m-d');
                    $q->whereDate('tanggal','<=',  $date2);
                });
            }
        }

        if (isset($dto['izin_sakit_uuid']) and $dto['izin_sakit_uuid'] != '') {
            $model->where('uuid', $dto['izin_sakit_uuid']);
            $data = $model->first();
        } else {
            if (isset($dto['with_pagination'])) {
                $this->results['pagination'] = $this->paginationDetail($dto['per_page'], $dto['page'], $model->count());
                $model = $this->paginateData($model, $dto['per_page'], $dto['page']);
            }
            $data = $model->get();
        }

        $this->results['message'] = "Izin sakit successfully fetched";
        $this->results['data'] = $data;
    }

    public function rules($dto)
    {
        return [
            'izin_sakit_uuid' => ['nullable', 'uuid', new ExistsUuid('izin_sakits')]
        ];
    }
}
