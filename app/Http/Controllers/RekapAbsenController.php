<?php
namespace App\Http\Controllers;

use App\Helpers\DateTime;
use App\Models\Absensi;
use App\Models\RekapIzinSakit;
use App\Traits\Identifier;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RekapAbsenController extends Controller 
{
    use Identifier;

    public function index(Request $request)
    {
        $breadcrumb = [
            ['link' => '/', 'name' => 'Dashboard'],
            ['link' => '/absensi', 'name' => 'Absensi']
        ];
        if (have_permission('dashboard_view')) {
            $request->merge([
                'per_page' => 5,
                // 'page' => $request->start / $request->length + 1,
                'with_pagination' => true,
                'search_param' => $request->search['value'] ?? null,
                'sort_by' => 'jumlah_point',
                'sort_type' => 'desc',
                'user_uuid' => $request->user_uuid,
            ]);

            $top_employee = app('GetPointUserService')->execute($request->all());
                        
            return datatables($top_employee['data'])->skipPaging()
                ->addColumn('WFO', function ($row) use ($request) {
                    $month = $request->input('month');
                    $months = DateTime::getArrayOfMonths();
                    $monthNumber = array_search($month, $months);
                    $year = $request->input('year'); 

                    $wfo_count = Absensi::where('deleted_at', null)
                        ->where('user_id', $row->user->id)
                        ->where('kategori_absensi_id', 2)
                        ->whereMonth('tanggal', $monthNumber)
                        ->whereYear('tanggal', $year)
                        ->count();
                    
                    return $wfo_count;
                })

                ->addColumn('WFH', function ($row) use ($request) {
                    $month = $request->input('month');
                    $months = DateTime::getArrayOfMonths();
                    $monthNumber = array_search($month, $months);
                    $year = $request->input('year'); 

                    $wfh_count = Absensi::where('deleted_at', null)
                        ->where('user_id', $row->user->id)
                        ->where('kategori_absensi_id', 1)
                        ->whereMonth('tanggal', $monthNumber)
                        ->whereYear('tanggal', $year)
                        ->count();
                    
                    return $wfh_count;
                })

                ->skipPaging()
            
                // ->with([
                //     "recordsTotal"    => $top_employee['pagination']['total_data'],
                //     "recordsFiltered" => $top_employee['pagination']['total_data'],
                // ])
                ->toJson();
        }

        return response()->json([
            'success' => false
        ], 403);
    }
}
