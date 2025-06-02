<?php

namespace App\Http\Controllers;

use App\Helpers\DateTime;
use App\Models\Absensi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\User;
use Illuminate\Support\Facades\Log;

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
    
        // Ambil user_ids sesuai role
        $user_ids = in_array(auth()->user()->userRole->role_id, $approved_role_all)
            ? User::pluck('id')->toArray()
            : [auth()->user()->id];
    
        // Ambil bulan & tahun dari request, fallback ke saat ini
        $monthName = $request->input('month') ?: Carbon::now('Asia/Jakarta')->translatedFormat('F');
        $year = $request->input('year') ?: Carbon::now('Asia/Jakarta')->format('Y');
    
        try {
            $monthNumber = Carbon::createFromFormat('F', $monthName)->month;
        } catch (\Exception $e) {
            $monthNumber = Carbon::now('Asia/Jakarta')->month;
        }
    
        Log::info('Filter bulan diterima: ' . $monthName . ', angka: ' . $monthNumber . ', tahun: ' . $year);
        Log::info('User IDs yang difilter: ' . json_encode($user_ids));
    
        // Ambil data dari tabel absensis yang sesuai filter
        $absensis = Absensi::with(['user.userInformation'])
            ->whereIn('user_id', $user_ids)
            ->whereMonth('tanggal', $monthNumber)
            ->whereYear('tanggal', $year)
            ->get();
    
        Log::info('Jumlah data absensi ditemukan: ' . $absensis->count());
    
        return DataTables::of($absensis)
        ->addIndexColumn()
        ->addColumn('nama_karyawan', fn($r) => optional($r->user->userInformation)->nama ?? '-')
        ->addColumn('WFO', function($r) {
            return $r->nama_kategori === 'WFO' ? 1 : 0;
        })
        ->addColumn('WFH', function($r) {
            return $r->nama_kategori === 'WFH' ? 1 : 0;
        })
        ->make(true);
    }    

}
