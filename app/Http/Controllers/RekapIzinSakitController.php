<?php

namespace App\Http\Controllers;

use App\Http\Requests\RekapIzinSakit\DeleteRekapIzinSakitRequest;
use App\Http\Requests\RekapIzinSakit\GetRekapIzinSakitRequest;
use App\Http\Requests\RekapIzinSakit\StoreRekapIzinSakitRequest;
use App\Http\Requests\RekapIzinSakit\UpdateRekapIzinSakitRequest;
use App\Models\RekapIzinSakit;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class RekapIzinSakitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (have_permission('rekap_izin_sakit_view')) {
            $breadcrumb = [
                ['link' => '/', 'name' => 'Dashboard'],
                ['link' => '/rekap_izin_sakit', 'name' => 'Rekap Izin Sakit']
            ];

            $users = app('GetUserService')->execute([
                'role_id_not_in' => [1]
            ]);

            return view('rekap_izin_sakit.index', [
                'breadcrumb' => breadcrumb($breadcrumb),
                'users' => $users['data']
            ]);
        }
        return view('errors.403');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function grid(GetRekapIzinSakitRequest $request)
    {
        if (have_permission('rekap_izin_sakit_view')) {
            $query = DB::table('izin_sakits')
            ->whereNull('deleted_at');

        if ($request->filled('user_uuid')) {
            $user_id = User::where('uuid', $request->user_uuid)->value('id');
            if ($user_id) {
                $query->where('user_id', $user_id);
            }
        }

        if ($request->filled('month')) {
            $query->whereMonth('tanggal', $request->month);
        }

        if ($request->filled('year')) {
            $query->whereYear('tanggal', $request->year);
        }

        $izin_sakits = $query->get();

        // calculate jumlah izin sakit per user
        $izin_sakits = $izin_sakits->groupBy('user_id')->map(function ($group) {
            return [
                'user_id' => $group->first()->user_id,
                'nama_karyawan' => $group->first()->nama_karyawan,
                'bulan' => Carbon::parse($group->first()->tanggal)->locale('id')->translatedFormat('F'),
                'tahun' => Carbon::parse($group->first()->tanggal)->locale('id')->translatedFormat('Y'),
                'jumlah_izin_sakit' => $group->count(),
            ];
        });
    
        return DataTables::of($izin_sakits)
            ->skipPaging()
            ->toJson();
        }
    }

    public function exportPdf(Request $request)
    {
        $query = DB::table('izin_sakits')
            ->whereNull('deleted_at');

        if ($request->filled('user_uuid')) {
            $user_id = User::where('uuid', $request->user_uuid)->value('id');
            if ($user_id) {
                $query->where('user_id', $user_id);
            }
        }

        if ($request->filled('month')) {
            $query->whereMonth('tanggal', $request->month);
        }

        if ($request->filled('year')) {
            $query->whereYear('tanggal', $request->year);
        }

        $izin_sakits = $query->get();

        // calculate jumlah izin sakit per user
        $izin_sakits = $izin_sakits->groupBy('user_id')->map(function ($group) {
            return [
                'user_id' => $group->first()->user_id,
                'nama_karyawan' => $group->first()->nama_karyawan,
                'bulan' => Carbon::parse($group->first()->tanggal)->locale('id')->translatedFormat('F'),
                'tahun' => Carbon::parse($group->first()->tanggal)->locale('id')->translatedFormat('Y'),
                'jumlah_izin_sakit' => $group->count(),
            ];
        });

        $pdf = Pdf::loadView('pdf.rekap_izin_sakit', [
            'izin_sakits' => $izin_sakits,
        ]);

        $file_name = "Laporan_Rekap_Izin_Sakit_" . date('Y-m-d_H-i-s');
        return $pdf->stream($file_name . ".pdf");
    }
}     