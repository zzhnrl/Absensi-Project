<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exceptions\CustomException;
use App\Http\Requests\Absensi\StoreAbsensiRequest;
use App\Http\Requests\Absensi\GetAbsensiRequest;
use App\Models\Absensi;
use App\Models\PointUser;
use App\Models\KategoriAbsensi;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AbsensiController extends Controller
{
    public function index()
    {
        $breadcrumb = [
            ['link' => '/', 'name' => 'Dashboard'],
            ['link' => '/absensi', 'name' => 'Absensi']
        ];

        $kategori_absensis = app('GetKategoriAbsensiService')->execute([]);
        $users = app('GetUserService')->execute([
            'role_id_not_in' => [1]
        ]);

        return view('absensi.index', [
            'breadcrumb' => breadcrumb($breadcrumb),
            'kategori_absensis' => $kategori_absensis['data'],
            'users' => $users['data']
        ]);

        return view('errors.403');
    }

    public function create(Request $request) 
    {
        if (have_permission('absensi_create')) {
            $breadcrumb = [
                ['link' => '/', 'name' => 'Dashboard'],
                ['link' => '/absensi', 'name' => 'Absensi'],
                ['link' => '/absensi/create', 'name' => 'Create']
            ];

            $users = app('GetUserService')->execute([]);
            $user = auth()->id();

            if (auth()->user()->id === 1) {
                return view('absensi.create', [
                    'breadcrumb' => breadcrumb($breadcrumb),
                    'users' => $users['data'],
                    'kategori_absensis' => KategoriAbsensi::where('is_active', 1)->get()
                ]);
            }

            $hariIni = now()->toDateString(); 
            $sudahAbsensi = Absensi::where('user_id', $user)
                ->where('tanggal', $hariIni) 
                ->exists();

            $office = \App\Models\OfficeLocation::first();
                    if (!$office) {
            return redirect()->route("absensi")->with('danger', 'Lokasi kantor belum diatur.');
        }

            if (!$sudahAbsensi) {
                return view('absensi.create', [
                    'breadcrumb' => breadcrumb($breadcrumb),
                    'users' => $users['data'],
                    'kategori_absensis' => KategoriAbsensi::where('is_active', 1)->get(),
                    'office' => $office
                ]);
            } else {
                $alert = 'success';
                $message = 'Anda sudah melakukan absensi hari ini.';
                return redirect()->route("absensi")->with($alert, $message);
            }
        }
        return view('errors.403');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
public function store(Request $request)
{
    DB::beginTransaction();
    try {
        $authUser = auth()->user();

        // Log data yang dikirim
        Log::info('Data request diterima:', $request->all());

        // Cek apakah user adalah Admin (id = 1) atau user biasa
        if ($authUser->id == 1) {
            $user = User::where('uuid', $request->user_uuid)->firstOrFail();
        } else {
            $user = $authUser;
        }

        Log::info('User yang akan digunakan:', [
            'authUser_id' => $authUser->id,
            'request_user_uuid' => $request->user_uuid ?? 'NULL',
            'user_id' => $user->id,
            'user_uuid' => $user->uuid,
        ]);

        // Ambil kategori absensis berdasarkan UUID
        Log::info('Mencari kategori absensis dengan UUID:', ['uuid' => $request->kategori_absensi_uuid]);

        $kategori_absensis = KategoriAbsensi::where('uuid', $request->kategori_absensi_uuid)->first();

        if (!$kategori_absensis) {
            Log::warning('Kategori absensis tidak ditemukan:', ['uuid' => $request->kategori_absensi_uuid]);
            return redirect()->back()->with('danger', 'Kategori absensis tidak ditemukan');
        }

        Log::info('Kategori absensis ditemukan:', $kategori_absensis->toArray());

        // Ambil nama karyawan dari tabel user_informations berdasarkan user_id
        $nama_karyawan = DB::table('user_informations')
            ->where('user_id', $user->id)
            ->value('nama') ?? 'Unknown';

        Log::info('Nama karyawan yang digunakan:', ['nama_karyawan' => $nama_karyawan]);

        // Konversi tanggal menjadi format bulan dan tahun dalam bahasa Indonesia
        $bulan = Carbon::parse($request->date)->locale('id')->translatedFormat('F');
        $tahun = Carbon::parse($request->date)->locale('id')->translatedFormat('Y');

        // Cek apakah user sudah memiliki point pada bulan & tahun yang sama
        $point_user = PointUser::whereNull('deleted_at')
            ->where('user_id', $user->id)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->first();

        if ($point_user) {
            // Update jumlah point jika data sudah ada
            $point_user->update([
                'jumlah_point' => $point_user->jumlah_point + $kategori_absensis->point
            ]);
            Log::info('Point user diperbarui:', $point_user->toArray());
        } else {
            // Buat record baru jika belum ada
            $point_user = PointUser::create([
                'uuid' => Str::uuid(),
                'user_id' => $user->id,
                'user_uuid' => $user->uuid,
                'nama_karyawan' => $nama_karyawan,
                'bulan' => $bulan,
                'tahun' => $tahun,
                'jumlah_point' => $kategori_absensis->point,
            ]);
            Log::info('Point user baru dibuat:', $point_user->toArray());
        }

        // Menentukan jam masuk
        $jam_masuk = now()->format('H:i');

        // Menentukan status absensi berdasarkan jam masuk
        $jamSekarang = now()->hour * 60 + now()->minute;
        if ($jamSekarang >= 540 && $jamSekarang <= 600) {
            $status_absen = "Hadir";
        } elseif ($jamSekarang >= 601 && $jamSekarang <= 1020) {
            $status_absen = "Terlambat";
        } else {
            $status_absen = "Alpha / Tidak Hadir";
        }

        // **Perbaikan: Pastikan semua kolom yang diperlukan tidak NULL**
        $absensi = Absensi::create([
            'uuid' => Str::uuid(), // ✅ **Tambahkan UUID agar tidak NULL**
            'user_id' => $user->id,
            'user_uuid' => $user->uuid,
            'kategori_absensi_id' => $kategori_absensis->id,
            'tanggal' => now()->toDateString(),
            'nama_kategori' => $kategori_absensis->name,
            'nama_karyawan' => $nama_karyawan,
            'jumlah_point' => $kategori_absensis->point ?? 0,
            'kategori_absensi_uuid' => $request->kategori_absensi_uuid,
            'keterangan' => $request->keterangan ?? '-',
            'jam_masuk' => $jam_masuk,
            'status_absen' => $status_absen,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Log::info('Absensi berhasil disimpan:', $absensi->toArray());

        DB::commit();
        return redirect()->back()->with('success', 'Absensi berhasil disimpan');
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error saat menyimpan absensi:', ['error' => $e->getMessage()]);
        return redirect()->back()->with('danger', 'Terjadi kesalahan saat menyimpan absensi');
    }
}




    /**
     * Show the form for editing the specified resource.
     *
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */
    public function destroy($uuid)
    {
        if (!have_permission('absensi_delete')) {
            return view('errors.403');
        }

        DB::beginTransaction();
        try {
            $absensi = Absensi::with('kategori')->where('uuid', $uuid)->firstOrFail();
            $kategori = $absensi->kategori;

            $point_user = PointUser::where('deleted_at', null)
                ->where('nama_karyawan', $absensi->nama_karyawan)
                ->where('bulan', Carbon::parse($absensi->tanggal)->locale('id')->translatedFormat('F'))
                ->where('tahun', Carbon::parse($absensi->tanggal)->format('Y'))
                ->first();

            if ($point_user) {
                $point_akhir = $point_user->jumlah_point - $kategori->point;
                app('UpdatePointUserService')->execute([
                    'point_user_id' => $point_user->id,
                    'jumlah_point' => $point_akhir,
                ], true);
            }

            app('DeleteAbsensiService')->execute(['absensi_uuid' => $uuid], true);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Absensi berhasil dihapus dan poin telah diperbarui.'
            ], 200);
        } catch (\Exception $ex) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $ex->getMessage()
            ], 500);
        }
    }

    /**
     * Display a listing of the resource in datatable formats.
     *
     * @return \Illuminate\Http\Response
     */
    public function grid(GetAbsensiRequest $request)
    {
        if (have_permission('absensi_view')) {
            $approved_role_all = [1,2];
            $user_ids = in_array(auth()->user()->userRole->role_id, $approved_role_all)
            ? User::pluck('id')->toArray()
            : [auth()->user()->id];

            $request->merge([
                'per_page' => $request->length,
                'page' => $request->start / $request->length + 1,
                'with_pagination' => true,
                'search_param' => $request->search['value'],
                'user_id_in' => $user_ids
            ]);
            
            $absensi = app('GetAbsensiService')->execute($request->all());

            return datatables($absensi['data'])->skipPaging()
                ->with([
                    "recordsTotal"    => $absensi['pagination']['total_data'],
                    "recordsFiltered" => $absensi['pagination']['total_data'],
                ])
                ->rawColumns(['action'])
                ->addColumn('action', function ($row) {
                    if (!empty($row->id)) {
                        $action = [];
                        (have_permission('absensi_delete')) ? array_push($action, "<button value='$row->uuid' class='delete dropdown-item font-action' >Delete</button>") : null;
                        return generate_action_button($action);
                    }
                })
                ->toJson();
        }
        return view('errors.403');
    }
}
