<?php

namespace App\Http\Controllers;

use App\Helpers\DateTime;
use App\Models\Absensi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\User;

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

        // Cek role, jika termasuk role 1 atau 2 maka ambil semua user_id, jika tidak hanya ambil user yang sedang login
        $user_ids = in_array(auth()->user()->userRole->role_id, $approved_role_all)
            ? User::pluck('id')->toArray()
            : [auth()->user()->id];

        // fallback nama bulan
        $monthName = $request->input('month')
                ?: Carbon::now('Asia/Jakarta')->translatedFormat('F');
        $year      = $request->input('year')
                ?: Carbon::now('Asia/Jakarta')->format('Y');

        // parse nama bulan ke angka
        try {
            $monthNumber = Carbon::createFromFormat('F', $monthName, 'id')->month;
        } catch (\Exception $e) {
            $monthNumber = Carbon::now('Asia/Jakarta')->month;
        }

        // Ambil data dari service dan filter berdasarkan user_ids
        $top = collect(app('GetPointUserService')->execute($request->all())['data'])
            ->filter(fn($row) => in_array($row->user_id, $user_ids)); // hanya data yang sesuai

        return DataTables::of($top)
            ->addIndexColumn()
            ->addColumn('nama_karyawan', fn($r) => $r->user->userInformation->nama)
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
