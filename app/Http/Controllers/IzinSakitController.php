<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exceptions\CustomException;
use App\Http\Requests\IzinSakit\StoreIzinSakitRequest;
use App\Http\Requests\IzinSakit\GetIzinSakitRequest;
use App\Models\HistoryPointUser;
use App\Models\IzinSakit;
use App\Models\RekapIzinSakit;
use App\Models\PointUser;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;

class IzinSakitController extends Controller
{
    public function index()
    {
        $breadcrumb = [
            ['link' => '/', 'name' => 'Dashboard'],
            ['link' => '/izin_sakit', 'name' => 'Izin Sakit']
        ];

        $users = app('GetUserService')->execute([
            'role_id_not_in' => [1]
        ]);

        return view('izin_sakit.index', [
            'breadcrumb' => breadcrumb($breadcrumb),
            'users' => $users['data']
        ]);

        return view('errors.403');
    }

    public function create()
    {
        if (have_permission('izin_sakit_create')) {
            $breadcrumb = [
                ['link' => '/', 'name' => 'Dashboard'],
                ['link' => '/izin_sakit', 'name' => 'Izin Sakit'],
                ['link' => '/izin_sakit/create', 'name' => 'Create']
            ];

            $users = app('GetUserService')->execute([]);

            return view('izin_sakit.create', [
                'breadcrumb' => breadcrumb($breadcrumb),
                'users' => $users['data'],
            ]);
        }
        return view('errors.403');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response 
     */
    public function store(StoreIzinSakitRequest $request)
    {
        // return $request;
        DB::beginTransaction();
        try {
            if (auth()->user()->id == 1){
                $user = app('GetUserService')->execute([
                    'user_uuid' => $request->user_uuid,
                ])['data'];

            } else {
                $user = app('GetUserService')->execute([
                    'user_uuid' => auth()->user()->uuid,
                ])['data'];
            }

            Log::info('Data Request:', $request->all());

            $file_storage = null;
            if ($request->hasFile('imagess')) {
                $file_storage = app('StoreFileStorageService')->execute([
                    'file' => $request->file('imagess'),
                    'location' => 'imagess/' . now()->format('Y-m-d'),
                    'filesystem' => 'public',
                    'compress' => false
                ], true);
            }

            $point_user = app('GetPointUserService')->execute([
                'point_user_uuid' => $request->point_user_uuid,
            ])['data'];

            $jumlah_izin_sakit = app('GetRekapIzinSakitService')->execute([
                'rekap_izin_sakit_uuid' => $request->rekap_izin_sakit_uuid,
            ])['data'];

            // return $jumlah_izin_sakit;

            $bulan = Carbon::parse($request->date)->locale('id')->translatedFormat('F');
            $tahun = Carbon::parse($request->date)->locale('id')->translatedFormat('Y');
            $nama_karyawan = $user->userInformation->nama;

            $rekap_izin_sakit = RekapIzinSakit::whereNull('deleted_at')
                ->where('nama_karyawan', $nama_karyawan)
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->first();

            $point_user = PointUser::where('deleted_at', null)
                ->where('nama_karyawan', $user->userInformation->nama) 
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->first();
            

                $now         = Carbon::now('Asia/Jakarta');


            if (!$rekap_izin_sakit) {
                // Jika tidak ada data rekap izin sakit, buat data baru
                $input_dto = [
                    'user_uuid' => $user->uuid,
                    'nama_karyawan' => $nama_karyawan,
                    'bulan' => $bulan,
                    'tahun' => $tahun, 
                    'jumlah_izin_sakit' => 1,
                ];
                app('StoreRekapIzinSakitService')->execute($input_dto, true);
            } else {
                // Update jumlah izin sakit
                $rekap_izin_sakit->jumlah_izin_sakit += 1;

                app('UpdateRekapIzinSakitService')->execute([
                    'rekap_izin_sakit_id' => $rekap_izin_sakit->id,
                    'jumlah_izin_sakit' => $rekap_izin_sakit->jumlah_izin_sakit,
                ], true);

                if ($rekap_izin_sakit->jumlah_izin_sakit >= 4) {
                    // Kurangi poin jika jumlah izin sakit mencapai 4 atau lebih
                    $point_user = PointUser::where('nama_karyawan', $user->userInformation->nama)->first();
                    if ($point_user) {
                        $point_user->decrement('jumlah_point', 3);
                                // Catat ke history_point_users
        HistoryPointUser::create([
            'user_id'          => $user->id,
            'jumlah_point'     => $point_user->jumlah_point,  // total poin setelah dikurangi
            'perubahan_point'  => -3,
            'tanggal' => $now->toDateString(),                         // perubahan poin
        ]);
                    } else {
                        throw new \Exception("User point not found");
                    }
                }
            }

            $now         = Carbon::now('Asia/Jakarta');

            $input_dto = [
                'user_uuid' => $user->uuid,
                'nama_karyawan' => $user->userInformation->nama,
                'tanggal' => $now->toDateString(),
                'photo_uuid' => $file_storage['data']['uuid'] ?? null,
                'keterangan' => $request->keterangan,
            ];

            $user = app('StoreIzinSakitService')->execute($input_dto, true);

            
        DB::commit();

// Kirim email ke semua admin (role_id 1) dan super‐admin (role_id 2)
$adminUsers = \App\Models\User::whereIn('role_id', [1, 2])->get();

foreach ($adminUsers as $admin) {
    Mail::to($admin->email)->send(new \App\Mail\IzinSakitNotification(
        $nama_karyawan,
        now()->format('Y-m-d'),
        $request->keterangan
    ));
}


            $alert = 'success';
            $message = 'Izin sakit berhasil dibuat';
            DB::commit();
            return redirect()->route('izin_sakit')->with($alert, $message);
        } catch (\Exception $ex) {
            DB::rollback();
            $alert = 'danger';
            $message = $ex->getMessage();
            return redirect()->back()->withInput()->with($alert, $message);
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
        if (have_permission('izin_sakit_delete')) {
            DB::beginTransaction();
            try {
                $input_dto = [
                    'izin_sakit_uuid' => $uuid
                ];
                app('DeleteIzinSakitService')->execute($input_dto, true);

                DB::commit();
                $message = 'Izin sakit berhasil dihapus';
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 200);
            } catch (\Exception $ex) {
                DB::rollback();
                $message = $ex->getMessage();
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 500);
            }
        }
        return view('errors.403');
    }

    public function grid(GetIzinSakitRequest $request)
    {
        if (! have_permission('izin_sakit_view')) {
            return response()->json([
                'data'            => [],
                'recordsTotal'    => 0,
                'recordsFiltered' => 0,
                'error'           => 'Forbidden',
            ], 403);
        }
    
        try {
            $approved_role_all = [1,2];
            $user_ids = in_array(auth()->user()->userRole->role_id, $approved_role_all)
                ? User::pluck('id')->toArray()
                : [auth()->user()->id];
    
            // Ambil filter dari query params
            $month = $request->query('month');
            $year = $request->query('year');
            $user_uuid = $request->query('user_uuid');
    
            $request->merge([
                'per_page' => $request->length,
                'page' => $request->start / $request->length + 1,
                'with_pagination' => true,
                'search_param' => $request->search['value'],
                'user_id_in' => $user_ids
            ]);
    
            // Build query manual (bisa juga dimasukkan ke service)
            $query = IzinSakit::whereNull('deleted_at');
    
            // Filter user berdasarkan UUID kalau ada
            if ($user_uuid) {
                $user = User::where('uuid', $user_uuid)->first();
                if ($user) {
                    $query->where('user_id', $user->id);
                }
            } else {
                // Filter hanya untuk user yang boleh lihat data
                $query->whereIn('user_id', $user_ids);
            }
    
            // Filter bulan
            if ($month) {
                $query->whereMonth('tanggal', $month);
            }
    
            // Filter tahun
            if ($year) {
                $query->whereYear('tanggal', $year);
            }
    
            // Tambahkan pagination, search, dan sorting jika perlu
            // Contoh sederhana pagination manual:
            $total = $query->count();
    
            if ($request->per_page) {
                $query->skip(($request->page - 1) * $request->per_page)
                      ->take($request->per_page);
            }
    
            $izin_sakit = $query->get();
    
            return datatables($izin_sakit)
                ->skipPaging()
                ->with([
                    "recordsTotal"    => $total,
                    "recordsFiltered" => $total,
                ])
                ->rawColumns(['action', 'pbukti'])
                ->addColumn('pbukti', function ($row) {
                    if (isset($row->photo_id)) {
                        return $row->photo->generateUrl()->url;  // kirim URL murni
                    }
                    return asset('img/no_picture.png'); // kirim URL default
                })
                
                ->addColumn('nama_karyawan', function($row) {
                    return $row->user->userInformation->nama ?? '-';
                })
                ->addColumn('action', function ($row) {
                    if (!empty($row->id)) {
                        $action = [];
                        (have_permission('izin_sakit_delete')) ? array_push($action, "<button value='$row->uuid' class='delete dropdown-item font-action'>Delete</button>") : null;
                        return generate_action_button($action);
                    }
                })
                ->toJson();

                Log::info('Filter month: ' . $month);
                Log::info('Filter year: ' . $year);
                Log::info('Filter user_uuid: ' . $user_uuid);
    
        } catch (\Throwable $e) {
            Log::error('IzinSakit grid error: '.$e->getMessage());
            return response()->json([
                'data'            => [],
                'recordsTotal'    => 0,
                'recordsFiltered' => 0,
                'error'           => $e->getMessage(),
            ], 500);
        }
    }
    
    public function exportPdf(Request $request)
    {
        $izin_sakits = IzinSakit::whereNull('deleted_at');

        if ($request->filled('month')) {
            $izin_sakits->whereMonth('tanggal', $request->month);
        }

        if ($request->filled('year')) {
            $izin_sakits->whereYear('tanggal', $request->year);
        }

        if ($request->filled('user_uuid')) {
            $user = User::where('uuid', $request->user_uuid)->first();
            if ($user) {
                $izin_sakits->where('user_id', $user->id);
            } else {
                $izin_sakits->whereNull('user_id');
            }
        }
    
        // Get manager signature if available
        $manager_signature = null;
        if (auth()->check() && auth()->user()->userInformation) {
            $manager_signature = auth()->user()->userInformation->signatureFile->url ?? null;
        }
        
        $pdf = Pdf::loadView('pdf.izin_sakit', [
            'izin_sakits' => $izin_sakits->get(),
            'manager_signature' => $manager_signature
        ]);
        $file_name = "Laporan_Izin_Sakit_" . date('Y-m-d_H-i-s');
        return $pdf->stream($file_name . ".pdf");
    }
}