<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index() {
        $currentMonth = now()->format('m'); // Bulan saat ini
        $currentYear = now()->format('Y'); // Tahun saat ini
    
        // Ambil data awal untuk bulan dan tahun saat ini
        $total_karyawan = app('GetDashboardJumlahKaryawanService')->execute([]);
        $total_cuti = app('GetDashboardJumlahCutiService')->execute([
            'month' => $currentMonth,
            'year' => $currentYear,
        ]);
    
        // Return data ke view
        return view('home', [
            'total_karyawan' => $total_karyawan['data'],
            'total_cuti' => $total_cuti['data'],
            'defaultMonth' => $currentMonth,
            'defaultYear' => $currentYear,
        ]);
    }

    // public function index()
    // {
    //     if (have_permission('home_view')) {
    //         // Fetch data from service
    //         $points = app('GetPointUserService')->execute([]);

    //         // Extract labels (e.g., Nama Karyawan) and data (Jumlah Point)
    //         $chartLabels = collect($points['data'])->pluck('nama_karyawan')->toArray();
    //         $chartData = collect($points['data'])->pluck('jumlah_point')->toArray();

    //         // Pass data to the view
    //         return view('dashboard', compact('chartLabels', 'chartData'));
    //     }

    //     return view('errors.403');
    // }

}
