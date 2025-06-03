<?php

namespace App\Services\Absensi;

use App\Models\Absensi;
use App\Models\KategoriAbsensi;
use App\Models\User;
use App\Services\DefaultService;
use App\Services\ServiceInterface;
use App\Rules\ExistsUuid;
use Carbon\Carbon;

class GetAbsensiService extends DefaultService implements ServiceInterface
{
    public function process($dto)
    {
        $dto['per_page'] = $dto['per_page'] ?? 10;
        $dto['page'] = $dto['page'] ?? 1;
        $dto['sort_by'] = $dto['sort_by'] ?? 'updated_at';
        $dto['sort_type'] = $dto['sort_type'] ?? 'desc';

        $model = Absensi::where('deleted_at', null)
            ->orderBy($dto['sort_by'], $dto['sort_type']);

        if (isset($dto['search_param']) and $dto['search_param'] != null) {
            $model->where(function ($q) use ($dto) {
                $q->where('nama', 'ILIKE', '%' . $dto['search_param'] . '%')
                ->orwhere('nama_kategori', 'ILIKE', '%' . $dto['search_param'] . '%');
            });
        }

        if (isset($dto['user_id_in'])) {
            $model->whereIn('user_id', $dto['user_id_in']);
        }

        if (isset($dto['kategori_absensi_uuid']) and $dto['kategori_absensi_uuid'] != '') {
            $kategori_absensi_id = $this->findIdByUuid(KategoriAbsensi::query(), $dto['kategori_absensi_uuid']);
            $model->where('kategori_absensi_id', $kategori_absensi_id);
        }

        if (isset($dto['user_uuid']) and $dto['user_uuid'] != '') {
            $user_id = $this->findIdByUuid(User::query(), $dto['user_uuid']);
            $model->where('user_id', $user_id);
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

        if (isset($dto['absensi_uuid']) and $dto['absensi_uuid'] != '') {
            $model->where('uuid', $dto['absensi_uuid']);
            $data = $model->first();
        } else {
            if (isset($dto['with_pagination'])) {
                $this->results['pagination'] = $this->paginationDetail($dto['per_page'], $dto['page'], $model->count());
                $model = $this->paginateData($model, $dto['per_page'], $dto['page']);
            }
            $data = $model->get();
        }


                // Generate full URL using asset() for correct domain and port
                if ($data instanceof \Illuminate\Support\Collection) {
                    $data = $data->map(function ($item) {
                        $item->bukti_foto_url = $item->bukti_foto_dikantor
                            ? asset('storage/' . ltrim($item->bukti_foto_dikantor, '/'))
                            : null;
                        return $item;
                    });
                } elseif ($data) {
                    $data->bukti_foto_url = $data->bukti_foto_dikantor
                        ? asset('storage/' . ltrim($data->bukti_foto_dikantor, '/'))
                        : null;
                }
        

        $this->results['message'] = "Absensi successfully fetched";
        $this->results['data'] = $data;
    }

    public function rules($dto)
    {
        return [
            'absensi_uuid' => ['nullable', 'uuid', new ExistsUuid('absensis')]
        ];
    }
}
