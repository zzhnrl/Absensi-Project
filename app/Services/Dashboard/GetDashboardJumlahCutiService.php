<?php
namespace App\Services\Dashboard;

use App\Models\StatusCuti;
use App\Services\DefaultService;
use App\Services\ServiceInterface;
use Illuminate\Support\Facades\DB;

class GetDashboardJumlahCutiService extends DefaultService implements ServiceInterface
{
    public function process($dto) 
    {
        $bulan = $dto['month'] ?? date('m');
        $tahun = $dto['year'] ?? date('Y');

        //Jumlah Cuti
        $cuti_statuses = StatusCuti::where('deleted_at', null)
        ->select('id', 'nama')->get();
        
        $cutiCount = [];
        foreach ($cuti_statuses as $status) {
            $cutiCount[$status->nama] = 0;
        }

        $cutis = DB::select("
        SELECT 
            c.status_cuti_id, 
            EXTRACT(MONTH FROM c.tanggal_mulai) AS month,
            EXTRACT(YEAR FROM c.tanggal_mulai) AS year,
            COUNT(c.id) as total
        FROM 
            cutis c
        INNER JOIN 
            status_cutis cs ON c.status_cuti_id = cs.id
        WHERE 
            c.deleted_at IS NULL
            AND EXTRACT(MONTH FROM c.tanggal_mulai) = ?
            AND EXTRACT(YEAR FROM c.tanggal_mulai) = ?
        GROUP BY 
            c.status_cuti_id, 
            EXTRACT(MONTH FROM c.tanggal_mulai),
            EXTRACT(YEAR FROM c.tanggal_mulai)
        ", [$bulan, $tahun]);
        
        $totalLeaves = 0;

        foreach ($cutis as $row) {
            $totalLeaves += $row->total;

            $status = $cuti_statuses->where('id', $row->status_cuti_id)->first();
            if ($status) {
                $cutiCount[$status->nama] = $row->total;
            }
        }

        $header = 
        [
            'jumlah_cuti' => $totalLeaves,
            'cutis' => $cutiCount
        ];
        
        return $this->results['data'] = $header;
    }
}