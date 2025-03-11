<?php
namespace App\Services\Dashboard;

use App\Helpers\DateTime;
use App\Services\DefaultService;
use App\Services\ServiceInterface;
use Illuminate\Support\Facades\DB;

class GetAbsensiDailyHistoryService extends DefaultService implements ServiceInterface
{
    public function process($dto)
    {
        $dto = $this->prepare($dto);

        $dates = collect($this->generateDateRange($dto['start_date'], $dto['end_date']));
        $categories = $this->getAllCategories();
        $model = $this->getSeriesDate($dto['start_date'], $dto['end_date']);

        $groupedData = collect($model)->groupBy('nama_kategori');

        $series = $categories->map(function ($category) use ($groupedData, $dates) {
            $items = $groupedData->get($category->name, collect());

            $data = $dates->map(function ($date) use ($items) {
                $item = $items->firstWhere('tanggal', $date);
                return $item ? $item->jumlah : 0;
            })->toArray();

            return [
                'name' => $category->name,
                'data' => $data,
            ];
        })->toArray();

        $this->results['series'] = $series;
        $this->results['colors'] = ['#FF9900', '#00CC44', '#FF0066', '#808080'];
        $this->results['xAxis'] = $dates->map(fn($date) => (new \DateTime($date))->format('d'))->toArray();
    }

    public function prepare($dto)
    {
        if (isset($dto['month']) && isset($dto['year'])) {
            $month = str_pad($dto['month'], 2, '0', STR_PAD_LEFT);
            $year = $dto['year'];

            $dto['start_date'] = "{$year}-{$month}-01";
            $dto['end_date'] = date("Y-m-t", strtotime($dto['start_date']));
        } else {
            throw new \Exception('Month and Year are required');
        }

        return $dto;
    }

    public function getSeriesDate($date1, $date2)
    {
        $query = "
            WITH date_series AS (
                SELECT date_trunc('day', dd)::date AS series_date
                FROM generate_series(
                        ?::timestamp,
                        ?::timestamp,
                        '1 day'::interval
                    ) dd
            )
            SELECT 
                ds.series_date AS tanggal,
                ka.name AS nama_kategori,
                COUNT(a.id) AS jumlah
            FROM date_series ds
            CROSS JOIN kategori_absensis ka
            LEFT JOIN absensis a ON ds.series_date = a.tanggal AND a.kategori_absensi_id = ka.id
            WHERE a.is_active = 1 OR a.id IS NULL
            GROUP BY ds.series_date, ka.name
            ORDER BY ds.series_date, ka.name;
        ";

        $bindings = [$date1, $date2];

        return DB::select($query, $bindings);
    }

    private function getAllCategories()
    {
        return DB::table('kategori_absensis')->select('name')->get();
    }

    private function generateDateRange($start_date, $end_date, $dayOnly = false)
    {
        $start = new \DateTime($start_date);
        $end = new \DateTime($end_date);
        $end->modify('+1 day');

        $interval = new \DateInterval('P1D');
        $dateRange = new \DatePeriod($start, $interval, $end);

        return array_map(function ($date) use ($dayOnly) {
            return $dayOnly ? $date->format('d') : $date->format('Y-m-d');
        }, iterator_to_array($dateRange));
    }
}
