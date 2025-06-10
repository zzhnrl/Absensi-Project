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
        // 1) Default parameters
        $perPage    = $dto['per_page']    = $dto['per_page']  ?? 10;
        $page       = $dto['page']        = $dto['page']      ?? 1;
        $sortBy     = $dto['sort_by']     = $dto['sort_by']   ?? 'updated_at';
        $sortType   = $dto['sort_type']   = $dto['sort_type'] ?? 'desc';
        $withPaging = !empty($dto['with_pagination']);

        // 2) Base query
        $query = IzinSakit::query()
            ->whereNull('deleted_at')
            ->orderBy($sortBy, $sortType);

        // 3) Search & filters
        if (!empty($dto['search_param'])) {
            $keyword = $dto['search_param'];
            $query->where(function ($q) use ($keyword) {
                $q->where('nama_karyawan', 'ILIKE', '%' . $keyword . '%')
                  ->orWhere('keterangan', 'ILIKE', '%' . $keyword . '%');
            });
        }
        

        if (!empty($dto['user_id_in'])) {
            $query->whereIn('user_id', $dto['user_id_in']);
        }

        if (!empty($dto['month'])) {
            $query->where('bulan', $dto['month']);
        }

        if (!empty($dto['year'])) {
            $query->where('tahun', $dto['year']);
        }

        if (!empty($dto['user_uuid'])) {
            $uid = $this->findIdByUuid(User::query(), $dto['user_uuid']);
            $query->where('user_id', $uid);
        }

        if (!empty($dto['photo_uuid'])) {
            $pid = $this->findIdByUuid(FileStorage::query(), $dto['photo_uuid']);
            $query->where('photo_id', $pid);
        }

        // 4) Safe date_range parsing (abaikan jika 'undefined' atau kosong)
        $dr = trim((string)($dto['date_range'] ?? ''));
        if ($dr !== '' && strtolower($dr) !== 'undefined') {
            // pastikan format "YYYY-MM-DD - YYYY-MM-DD" atau "YYYY-MM-DD"
            $parts = preg_split('/\s*[-to]+\s*/i', $dr);
            $start = Carbon::parse($parts[0])->startOfDay();
            if (isset($parts[1])) {
                $end = Carbon::parse($parts[1])->endOfDay();
                $query->whereBetween('tanggal', [$start, $end]);
            } else {
                $query->whereDate('tanggal', $start);
            }
        }



        // 5) Handle detail vs list
        if (!empty($dto['izin_sakit_uuid'])) {
            // detail single record
            $this->results['data'] = $query->first();
            // tidak perlu pagination
        } else {
            // hitung total sebelum pagination
            $total = $query->count();

            if ($withPaging) {
                // simpan info pagination
                $this->results['pagination'] = $this->paginationDetail($perPage, $page, $total);
                // ambil hanya halaman $page
                $query = $this->paginateData($query, $perPage, $page);
            } else {
                // jika ingin full list tanpa pagination
                $this->results['pagination'] = [
                    'total_data' => $total,
                ];
            }

            // ambil data
            $this->results['data'] = $query->get();
        }

        $this->results['message'] = "Izin sakit successfully fetched";
    }

    public function rules($dto)
    {
        return [
            'izin_sakit_uuid' => ['nullable','uuid', new ExistsUuid('izin_sakits')],
        ];
    }
}