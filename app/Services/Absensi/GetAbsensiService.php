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
    // Mapping filter dari frontend
    if (isset($dto['karyawan_filter'])) {
        $dto['user_uuid'] = $dto['karyawan_filter'];
    }

    if (isset($dto['kategori_filter'])) {
        $dto['kategori_absensi_uuid'] = $dto['kategori_filter'];
    }

    $dto['per_page'] = $dto['per_page'] ?? 10;
    $dto['page'] = $dto['page'] ?? 1;
    $dto['sort_by'] = $dto['sort_by'] ?? 'updated_at';
    $dto['sort_type'] = $dto['sort_type'] ?? 'desc';

        $model = Absensi::query();
        $model->whereNull('deleted_at');
    
        if (!empty($dto['search_param'])) {
            $keyword = $dto['search_param'];
            $model->where(function ($q) use ($keyword) {
                $q->where('nama_karyawan', 'ILIKE', "%{$keyword}%")
                  ->orWhere('keterangan', 'ILIKE', "%{$keyword}%")
                  ->orWhere('status_absen', 'ILIKE', "%{$keyword}%")
                  ->orWhere('nama_kategori', 'ILIKE', "%{$keyword}%");
            });
        } else {
            if (isset($dto['user_id'])) {
                $model->where('user_id', $dto['user_id']);
            }
    
            if (isset($dto['user_id_in'])) {
                $model->whereIn('user_id', $dto['user_id_in']);
            }
    
            if (isset($dto['kategori_absensi_uuid']) && $dto['kategori_absensi_uuid'] != '') {
                $kategori_absensi_id = $this->findIdByUuid(KategoriAbsensi::query(), $dto['kategori_absensi_uuid']);
                $model->where('kategori_absensi_id', $kategori_absensi_id);
            }
    
            if (isset($dto['user_uuid']) && $dto['user_uuid'] != '') {
                $user_id = $this->findIdByUuid(User::query(), $dto['user_uuid']);
                $model->where('user_id', $user_id);
            }
    
            if (!empty($dto['date_range'])) {
                if (strpos($dto['date_range'], ' to ') !== false) {
                    $date = explode(' to ', $dto['date_range']);
                    $startDate = Carbon::parse($date[0])->format('Y-m-d');
                    $endDate = isset($date[1]) ? Carbon::parse($date[1])->format('Y-m-d') : $startDate;
    
                    $model->whereBetween('tanggal', [$startDate, $endDate]);
                } else {
                    try {
                        $model->whereDate('tanggal', Carbon::parse($dto['date_range'])->format('Y-m-d'));
                    } catch (\Exception $e) {
                        logger()->error("Invalid date_range format: " . $dto['date_range']);
                    }
                }
            }
        }
    
        // Filter absensi_uuid jika ada
        if (isset($dto['absensi_uuid']) && $dto['absensi_uuid'] != '') {
            $model->where('uuid', $dto['absensi_uuid']);
            $data = $model->first();
        } else {
            // Sorting
            $model->orderBy($dto['sort_by'], $dto['sort_type']);
    
            // Pagination jika diminta
            if (isset($dto['with_pagination']) && $dto['with_pagination']) {
                $this->results['pagination'] = $this->paginationDetail($dto['per_page'], $dto['page'], $model->count());
                $model = $this->paginateData($model, $dto['per_page'], $dto['page']);
            }
    
            $data = $model->get();
        }
    
        // Generate URL bukti foto
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
