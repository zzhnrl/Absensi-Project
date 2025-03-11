<?php
namespace App\Services\Dashboard;

use App\Models\KategoriAbsensi;
use App\Rules\ExistsUuid;
use App\Services\DefaultService;
use App\Services\ServiceInterface;
use Illuminate\Support\Facades\DB;

class SumAbsensiTotalByStatusService extends DefaultService implements ServiceInterface {

    public function process($dto)
    {
        $dto = $this->prepare($dto);
        // return $this->results['data'] = $dto;

        $query = $this->query($dto);
        $result = DB::select($query, [
            $dto['role_filter'],
            $dto['start_date'],
            $dto['end_date'],
            $dto['kategori_absensi_id'], 
        ]);
        // return $this->results['data'] = $result;

        $this->results['data'] = [
            'total' => (int) ($result[0]->count ?? 0)
        ];
        $this->results['message'] = "Total absensi successfully fetched";

        return $this->results;
    }

    public function prepare ($dto) {
        $dto['kategori_absensi_id'] = $this->findIdByUuid(KategoriAbsensi::query(), $dto['kategori_absensi_uuid']);
        return $dto;
    }

    public function rules ($dto) {
        return [
            'kategori_absensi_uuid' => ['required', 'uuid', new ExistsUuid('kategori_absensis')]
        ];
    }

    private function query($dto)
    {
        return "
            SELECT COUNT(DISTINCT user_id) as count
            FROM absensis
            WHERE is_active = 1
            AND deleted_at IS NULL
            AND user_id IN (
                SELECT users.id 
                FROM users 
                JOIN user_roles ON user_roles.user_id = users.id
                WHERE user_roles.role_id != ?
            )
            AND tanggal >= ?
            AND tanggal <= ?
            AND kategori_absensi_id = ?
            ";
    }
}

