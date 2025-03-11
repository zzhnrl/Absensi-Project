<?php

namespace App\Services\KategoriAbsensi;

use App\Models\KategoriAbsensi;
use App\Services\DefaultService;
use App\Services\ServiceInterface;
use App\Rules\ExistsUuid;

class GetKategoriAbsensiService extends DefaultService implements ServiceInterface
{

    public function process($dto)
    {
        $dto['per_page'] = $dto['per_page'] ?? 10;
        $dto['page'] = $dto['page'] ?? 1;
        $dto['sort_by'] = $dto['sort_by'] ?? 'updated_at';
        $dto['sort_type'] = $dto['sort_type'] ?? 'desc';

        $model = KategoriAbsensi::where('deleted_at', null)
            ->orderBy($dto['sort_by'], $dto['sort_type']);

        if (isset($dto['search_param']) and $dto['search_param'] != null) {
            $model->where(function ($q) use ($dto) {
                $q->where('name', 'ILIKE', '%' . $dto['search_param'] . '%')
                    ->orwhere('code', 'ILIKE', '%' . $dto['search_param'] . '%')
                    ->orwhere('point', 'ILIKE', '%' . $dto['search_param'] . '%');
            });
        }

        if (isset($dto['kategori_absensi_uuid']) and $dto['kategori_absensi_uuid'] != '') {
            $model->where('uuid', $dto['kategori_absensi_uuid']);
            $data = $model->first();
        } else {
            if (isset($dto['with_pagination'])) {
                $this->results['pagination'] = $this->paginationDetail($dto['per_page'], $dto['page'], $model->count());
                $model = $this->paginateData($model, $dto['per_page'], $dto['page']);
            }
            $data = $model->get();
        }

        $this->results['message'] = "Kategori absensi successfully fetched";
        $this->results['data'] = $data;
    }

    public function rules($dto)
    {
        return [
            'kategori_absensi_uuid' => ['nullable', 'uuid', new ExistsUuid('kategori_absensis')]
        ];
    }
}
