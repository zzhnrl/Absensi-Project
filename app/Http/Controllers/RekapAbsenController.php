<?php

namespace App\Http\Controllers;

use App\Helpers\DateTime;
use App\Models\Absensi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

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
        // … permission & merge …
    
        // fallback nama bulan
        $monthName = $request->input('month')
                   ?: Carbon::now('Asia/Jakarta')->translatedFormat('F');
        $year      = $request->input('year')
                   ?: Carbon::now('Asia/Jakarta')->format('Y');
    
        // parse nama bulan ke angka (butuh locale id di config/app.php -> 'locale' => 'id')
        try {
            $monthNumber = Carbon::createFromFormat('F', $monthName, 'id')->month;
        } catch (\Exception $e) {
            // kalau gagal parse, pakai bulan sekarang
            $monthNumber = Carbon::now('Asia/Jakarta')->month;
        }
    
        $top = app('GetPointUserService')->execute($request->all())['data'];
    
        return DataTables::of($top)
            ->addIndexColumn()
            ->addColumn('nama_karyawan', fn($r)=> $r->user->userInformation->nama)
            ->addColumn('WFO', fn($row) => 
                Absensi::where('user_id', $row->user->id)
                       ->where('nama_kategori', 'WFO')
                       ->whereMonth('tanggal', $monthNumber)
                       ->whereYear('tanggal', $year)
                       ->count()
            )
            ->addColumn('WFH', fn($row) => 
                Absensi::where('user_id', $row->user->id)
                       ->where('nama_kategori', 'WFH')
                       ->whereMonth('tanggal', $monthNumber)
                       ->whereYear('tanggal', $year)
                       ->count()
            )
            ->make(true);
    }
}
