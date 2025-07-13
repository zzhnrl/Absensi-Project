<?php

namespace App\Http\Controllers;

use App\Helpers\DateTime;
use App\Models\Absensi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class RekapAbsenController extends Controller
{
    /**
     * Tampilkan halaman Rekap Absensi
     */
    public function index()
    {
        // breadcrumb (opsional)
        $breadcrumb = [
            ['link' => '/',            'name' => 'Dashboard'],
            ['link' => '/rekap-absen', 'name' => 'Rekap Absensi'],
        ];

        return view('rekap_absen.index', [
            'breadcrumb'    => breadcrumb($breadcrumb),
            'months'        => DateTime::getArrayOfMonths(),
            'currentMonth'  => Carbon::now('Asia/Jakarta')->translatedFormat('F'),
            'currentYear'   => Carbon::now('Asia/Jakarta')->format('Y'),
        ]);
    }

    /**
     * DataTable AJAX: hitung WFO & WFH berdasarkan nama_kategori
     */

    public function data(Request $request)
    {
        $approved_role_all = [1, 2];

        $user_ids = in_array(auth()->user()->userRole->role_id, $approved_role_all)
            ? User::pluck('id')->toArray()
            : [auth()->user()->id];

        $monthName = $request->input('month') ?: Carbon::now('Asia/Jakarta')->translatedFormat('F');
        $year = $request->input('year') ?: Carbon::now('Asia/Jakarta')->format('Y');

        try {
            $monthNumber = Carbon::createFromFormat('F', $monthName)->month;
        } catch (\Exception $e) {
            $monthNumber = Carbon::now('Asia/Jakarta')->month;
        }

        // Gunakan join karena kita group by user_id
        $absensi_rekap = DB::table('absensis')
            ->select(
                'absensis.user_id',
                'user_informations.nama AS nama_karyawan',
                DB::raw("SUM(CASE WHEN absensis.nama_kategori = 'WFO' THEN 1 ELSE 0 END) AS total_wfo"),
                DB::raw("SUM(CASE WHEN absensis.nama_kategori = 'WFH' THEN 1 ELSE 0 END) AS total_wfh")
            )
            ->join('users', 'users.id', '=', 'absensis.user_id')
            ->leftJoin('user_informations', 'user_informations.user_id', '=', 'users.id')
            ->whereIn('absensis.user_id', $user_ids)
            ->whereMonth('absensis.tanggal', $monthNumber)
            ->whereYear('absensis.tanggal', $year)
            ->groupBy('absensis.user_id', 'user_informations.nama')
            ->get();

        return DataTables::of($absensi_rekap)
            ->addIndexColumn()
            ->addColumn('WFO', fn($r) => $r->total_wfo)
            ->addColumn('WFH', fn($r) => $r->total_wfh)
            ->make(true);
    }

    public function exportPdf(Request $request)
    {
        $approved_role_all = [1, 2];

        $user_ids = in_array(auth()->user()->userRole->role_id, $approved_role_all)
            ? User::pluck('id')->toArray()
            : [auth()->user()->id];

        $monthName = $request->input('month') ?: Carbon::now('Asia/Jakarta')->translatedFormat('F');
        $year = $request->input('year') ?: Carbon::now('Asia/Jakarta')->format('Y');

        try {
            $monthNumber = Carbon::createFromFormat('F', $monthName)->month;
        } catch (\Exception $e) {
            $monthNumber = Carbon::now('Asia/Jakarta')->month;
        }

        // Gunakan join karena kita group by user_id
        $absensi_rekap = DB::table('absensis')
            ->select(
                'absensis.user_id',
                'user_informations.nama AS nama_karyawan',
                DB::raw("SUM(CASE WHEN absensis.nama_kategori = 'WFO' THEN 1 ELSE 0 END) AS total_wfo"),
                DB::raw("SUM(CASE WHEN absensis.nama_kategori = 'WFH' THEN 1 ELSE 0 END) AS total_wfh")
            )
            ->join('users', 'users.id', '=', 'absensis.user_id')
            ->leftJoin('user_informations', 'user_informations.user_id', '=', 'users.id')
            ->whereIn('absensis.user_id', $user_ids)
            ->whereMonth('absensis.tanggal', $monthNumber)
            ->whereYear('absensis.tanggal', $year)
            ->groupBy('absensis.user_id', 'user_informations.nama')
            ->get();

        $pdf = Pdf::loadView('pdf.rekap_absensi', [
            'absensi_rekap' => $absensi_rekap,
        ]);

        $file_name = "Laporan_Rekap_Absensi_" . date('Y-m-d_H-i-s');
        return $pdf->stream($file_name . ".pdf");
    }
}
