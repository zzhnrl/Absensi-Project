<?php

namespace App\Services\RekapIzinSakit;

use App\Models\RekapIzinSakit;
use App\Services\DefaultService;
use App\Services\ServiceInterface;
use App\Rules\ExistsUuid;

class GetRekapIzinSakitService extends DefaultService implements ServiceInterface
{
    public function process($dto)
    {
        $dto['per_page'] = $dto['per_page'] ?? 10;
        $dto['page'] = $dto['page'] ?? 1;
        $dto['sort_by'] = $dto['sort_by'] ?? 'updated_at';
        $dto['sort_type'] = $dto['sort_type'] ?? 'desc';

        $model = RekapIzinSakit::where('deleted_at', null)
            ->orderBy($dto['sort_by'], $dto['sort_type']);

        if (isset($dto['search_param']) and $dto['search_param'] != null) {
            $model->where(function ($q) use ($dto) {
                $q->where('nama_karyawan', 'ILIKE', '%' . $dto['search_param'] . '%');
            });
        }

        if (isset($dto['user_id_in'])) {
            $model->whereIn('user_id', $dto['user_id_in']);
        }

        if (isset($dto['month'])) {
            $model->whereMonth('tanggal', $dto['month']);
        }

        if (isset($dto['year'])) {
            $model->whereYear('tanggal', $dto['year']);
        }

        if (isset($dto['rekap_izin_sakit_uuid']) and $dto['rekap_izin_sakit_uuid'] != '') {
            $model->where('uuid', $dto['rekap_izin_sakit_uuid']);
            $data = $model->first();
        } else {
            if (isset($dto['with_pagination'])) {
                $this->results['pagination'] = $this->paginationDetail($dto['per_page'], $dto['page'], $model->count());
                $model = $this->paginateData($model, $dto['per_page'], $dto['page']);
            }
            $data = $model->get();
        }

        $this->results['message'] = "Rekap izin sakit successfully fetched";
        $this->results['data'] = $data;
    }

    public function rules($dto)
    {
        return [
            'rekap_izin_sakit_uuid' => ['nullable', 'uuid', new ExistsUuid('rekap_izin_sakits')]
        ];
    }
}
