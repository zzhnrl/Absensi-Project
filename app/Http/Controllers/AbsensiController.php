<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exceptions\CustomException;
use App\Http\Requests\Absensi\StoreAbsensiRequest;
use App\Http\Requests\Absensi\GetAbsensiRequest;
use App\Models\Absensi;
use App\Models\HistoryPointUser;
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
    public function index(Request $request)
    {
        $breadcrumb = [
            ['link' => '/', 'name' => 'Dashboard'],
            ['link' => '/absensi', 'name' => 'Absensi']
        ];
    
        // Ambil data absensi berdasarkan filter
        $query = \App\Models\Absensi::query();
    
        // Filter tanggal
        if ($request->filled('date_range')) {
            $range = explode(' to ', $request->date_range);
            if (count($range) === 2) {
                $start = Carbon::parse($range[0])->startOfDay();
                $end = Carbon::parse($range[1])->endOfDay();
                $query->whereBetween('tanggal', [$start, $end]);
            }
        }
    
        // Filter karyawan
        if ($request->filled('karyawan_filter')) {
            $userId = User::where('uuid', $request->karyawan_filter)->value('id');
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                // UUID tidak ditemukan → kosongkan hasil
                $query->whereNull('user_id');
            }
        }
    
        // Filter kategori absensi
        if ($request->filled('kategori_filter')) {
            $query->where('nama_kategori', $request->kategori_filter);
        }
        
    
        $absensis = $query->latest()->get();
    
        $kategori_absensis = app('GetKategoriAbsensiService')->execute([]);
        $users = app('GetUserService')->execute([
            'role_id_not_in' => [1]
        ]);
    
        return view('absensi.index', [
            'breadcrumb' => breadcrumb($breadcrumb),
            'kategori_absensis' => $kategori_absensis['data'],
            'users' => $users['data'],
            'absensis' => $absensis,
            'filter' => $request->all() // untuk mengisi ulang input saat reload
        ]);
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

        $now         = Carbon::now('Asia/Jakarta');
        $jamMasuk    = $now->format('H:i');
        $totalMenit  = $now->hour * 60 + $now->minute;

        // Definisi rentang dalam menit
        $hadirStart     = 9 * 60;          // 09:00 → 540
        $hadirEnd       = 10 * 60;         // 10:00 → 600
        $terlambatStart = $hadirEnd + 1;   // 10:01 → 601
        $terlambatEnd   = 17 * 60;         // 17:00 → 1020
        $alphaStart     = $terlambatEnd + 1; // 17:01 → 1021

        // Hitung status berdasarkan waktu WIB
        if ($totalMenit >= $hadirStart && $totalMenit <= $hadirEnd) {
            $status_absen = "Hadir";
        } elseif ($totalMenit >= $terlambatStart && $totalMenit <= $terlambatEnd) {
            $status_absen = "Terlambat";
        } elseif ($totalMenit >= $alphaStart && $totalMenit < 24 * 60) {
            $status_absen = "Alpha / Tidak Hadir";
        } else {
            $status_absen = "Belum Absensi";
        }

        $request->validate([
    // …
    'bukti_foto_dikantor' => 'nullable|image|max:10240',
]);

if ($request->hasFile('bukti_foto_dikantor')) {
    $path = $request->file('bukti_foto_dikantor')
                    ->store('absensi/bukti', 'public');
    $data['bukti_foto_dikantor'] = $path;
}


        // Pastikan semua kolom yang diperlukan tidak NULL
// … setelah memanggil hasFile dan menyimpan $data['bukti_foto_dikantor']
$attributes = [
    'uuid'                  => Str::uuid(),
    'user_id'               => $user->id,
    'user_uuid'             => $user->uuid,
    'kategori_absensi_id'   => $kategori_absensis->id,
    'tanggal'               => $now->toDateString(),
    'nama_kategori'         => $kategori_absensis->name,
    'nama_karyawan'         => $nama_karyawan,
    'jumlah_point'          => $kategori_absensis->point ?? 0,
    'kategori_absensi_uuid' => $request->kategori_absensi_uuid,
    'keterangan'            => $request->keterangan ?? '-',
    'jam_masuk'             => $jamMasuk,
    'status_absen'          => $status_absen,
];

// Jika ada foto, tambahkan ke attributes
if (isset($data['bukti_foto_dikantor'])) {
    $attributes['bukti_foto_dikantor'] = $data['bukti_foto_dikantor'];
}

// Simpan Absensi
$absensi = Absensi::create($attributes);


// 1. Hitung perubahan poin berdasarkan nama kategori
if ($kategori_absensis->name === 'WFH') {
    $deltaPoint = 4;
} elseif ($kategori_absensis->name === 'WFO') {
    $deltaPoint = 6;
} else {
    // fallback pakai nilai default dari kategori
    $deltaPoint = $kategori_absensis->point ?? 0;
}

// 2. Pastikan $point_user sudah berisi record terbaru
//    (karena di atas kita sudah update atau create PointUser)
//    jadi total poin sekarang ada di $point_user->jumlah_point

// 3. Simpan ke history
$history = HistoryPointUser::create([
    'uuid'             => Str::uuid(),               // kalau tabel punya kolom uuid
    'user_id'          => $user->id,
    'jumlah_point'     => $point_user->jumlah_point, // total poin setelah update
    'perubahan_point'  => $deltaPoint,          
    'tanggal' => $now->toDateString(),     // selisih poin
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
                            // Kolom Bukti Foto
                            ->addColumn('bukti_foto', function($row) {
                                if ($row->bukti_foto_dikantor) {
                                    return "<img src=\"{$row->bukti_foto_url}\" 
                                                 style=\"max-width:50px;max-height:50px;border-radius:4px;\" 
                                                 alt=\"Bukti Foto\">";
                                }
                                return '-';
                            })
                            ->rawColumns(['bukti_foto','action'])
                            
                ->toJson();
        }
        return view('errors.403');
    }
}
