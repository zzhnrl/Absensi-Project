<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exceptions\CustomException;
use App\Http\Requests\Cuti\StoreCutiRequest;
use App\Http\Requests\Cuti\GetCutiRequest;
use App\Http\Requests\Cuti\SetujuiRequest;
use App\Http\Requests\Cuti\TolakRequest;
use App\Mail\CutiApprovalNotification;
use App\Mail\CutiNotification;
use App\Models\Cuti;
use App\Models\StatusCuti;
use App\Models\PointUser;
use App\Models\Role;
use App\Models\User;
use App\Models\UserInformation;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CutiController extends Controller
{
    public function index(Request $request)
    {
        // Breadcrumb untuk view
        $breadcrumb = [
            ['link' => '/', 'name' => 'Dashboard'],
            ['link' => '/cuti', 'name' => 'Cuti'],
        ];

        // Ambil data pengguna dan status cuti
        $users         = app('GetUserService')->execute(['role_id_not_in' => [1]]);
        $status_cutis  = app('GetStatusCutiService')->execute([]);
        // Panggil service Cuti untuk data dan sisa cuti
        $cutiService   = app('GetCutiService')->execute(['role_id_not_in' => [1], 'with_pagination' => false]);

        // Jika request AJAX, kembalikan JSON untuk DataTables
        if ($request->ajax()) {
            return response()->json([
                'data' => $cutiService['data'],
            ]);
        }

        // Render view biasa
        return view('cuti.index', [
            'breadcrumb'   => breadcrumb($breadcrumb),
            'users'        => $users['data'],
            'cutis'        => $cutiService['data'],
            'sisa_cuti'    => $cutiService['sisa_cuti'] ?? 0,
            'status_cutis' => $status_cutis['data'],
        ]);
    }

    public function create(Request $request)
    {
        if (have_permission('cuti_create')) {
            $breadcrumb = [
                ['link' => '/', 'name' => 'Dashboard'],
                ['link' => '/cuti', 'name' => 'Cuti'],
                ['link' => '/cuti/create', 'name' => 'Create']
            ];

            $users = app('GetUserService')->execute([]);
            $status_cutis = app('GetStatusCutiService')->execute([]);
            
            return view('cuti.create', [
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




     public function store(Request $request)
     {
         DB::beginTransaction();
         try {
             // Ambil user sesuai user_uuid di request, atau fallback ke user login
             if ($request->has('user_uuid') && !empty($request->user_uuid)) {
                 $user = app('GetUserService')->execute([
                     'user_uuid' => $request->user_uuid,
                 ])['data'];
             } else {
                 $user = app('GetUserService')->execute([
                     'user_uuid' => auth()->user()->uuid,
                 ])['data'];
             }
     
             Log::info("User UUID yang digunakan untuk proses cuti:", ['used_user_uuid' => $user->uuid]);
     
             // Validasi sisa cuti
             if ((int)$user->sisa_cuti <= 0) {
                 abort(403, 'Sisa cuti Anda sudah habis. Pengajuan cuti tidak diizinkan.');
             }
     
             // Fungsi hitung hari cuti dengan cek hari libur nasional dan weekend
             $totalCuti = $this->hitungHariCuti($request->tanggal_mulai, $request->tanggal_akhir);
     
             // Kurangi sisa cuti user
             $sisaCutiBaru = max(0, $user->sisa_cuti - $totalCuti);
     

     
             // Simpan data cuti
             $input_dto = [
                 'status_cuti_id' => 1,
                 'user_uuid' => $user->uuid,
                 'user_id' => $user->id,
                 'nama_karyawan' => $request->nama_karyawan,
                 'tanggal_mulai' => $request->tanggal_mulai,
                 'tanggal_akhir' => $request->tanggal_akhir,
                 'perihal' => $request->perihal,
                 'keterangan' => $request->keterangan,
                 'jenis_cuti' => $request->jenis_cuti,
                 'jumlah_cuti' => $totalCuti
             ];
     
             app('StoreCutiService')->execute($input_dto, true);
     
             // Kirim email notifikasi ke role 1 dan 2
             $emailsRole1 = DB::table('users')->where('role_id', 1)->pluck('email')->toArray();
             $emailsRole2 = DB::table('users')->where('role_id', 2)->pluck('email')->toArray();
             $recipients = array_unique(array_merge($emailsRole1, $emailsRole2));
     
             try {
                 Mail::to($recipients)->send(new \App\Mail\CutiNotification($input_dto));
                 Log::info('Email notifikasi cuti berhasil dikirim ke role 1 dan 2.', ['emails' => $recipients]);
             } catch (\Exception $emailEx) {
                 Log::error('Gagal mengirim email notifikasi cuti.', [
                     'error' => $emailEx->getMessage(),
                     'emails' => $recipients
                 ]);
             }
     
             DB::commit();
             return redirect()->route('cuti')->with('success', 'Cuti berhasil diajukan dan notifikasi dikirim ke admin');
     
         } catch (\Exception $ex) {
             DB::rollback();
             Log::error('Gagal menyimpan data cuti.', ['error' => $ex->getMessage()]);
             return redirect()->back()->withInput()->with('danger', $ex->getMessage());
         }
     }
     
     /**
      * Hitung hari cuti, exclude weekend dan hari libur nasional dari API
      */
      private function hitungHariCuti($tanggal_mulai, $tanggal_selesai)
      {
          $response = Http::get('https://api-harilibur.vercel.app/api');
      
          if (!$response->successful()) {
              throw new \Exception('Gagal mengambil data hari libur nasional.');
          }
      
          $hari_libur = collect($response->json())
              ->map(fn($item) => Carbon::parse($item['holiday_date'])->toDateString())
              ->toArray();
      
          Log::info('Daftar hari libur nasional:', $hari_libur);
      
          $mulai = Carbon::parse($tanggal_mulai);
          $selesai = Carbon::parse($tanggal_selesai);
          $jumlah_hari = 0;
      
          while ($mulai <= $selesai) {
              $tanggal = $mulai->toDateString();
              $isWeekend = $mulai->isWeekend();
              $isHariLibur = in_array($tanggal, $hari_libur);
      
              Log::info("Cek tanggal: $tanggal | Weekend: " . ($isWeekend ? 'YA' : 'TIDAK') . " | Hari Libur: " . ($isHariLibur ? 'YA' : 'TIDAK'));
      
              if (!$isWeekend && !$isHariLibur) {
                  $jumlah_hari++;
                  Log::info("Tanggal $tanggal dihitung sebagai hari cuti.");
              } else {
                  Log::info("Tanggal $tanggal TIDAK dihitung sebagai hari cuti.");
              }
      
              $mulai->addDay();
          }
      
          Log::info("Total hari cuti yang dihitung: $jumlah_hari");
      
          return $jumlah_hari;
      }


      public function hitungCuti(Request $request)
{
    $tanggalMulai = Carbon::parse($request->tanggal_mulai);
    $tanggalAkhir = Carbon::parse($request->tanggal_akhir);

    // Ambil hari libur nasional dari API
    $response = Http::get('https://api-harilibur.vercel.app/api');
    $hariLibur = collect($response->json())->pluck('holiday_date')->toArray();

    

    $hariLibur = array_merge($hariLibur);

    $totalCuti = 0;
    $current = $tanggalMulai->copy();

    while ($current->lte($tanggalAkhir)) {
        $isWeekend = $current->isWeekend();
        $isHariLibur = in_array($current->toDateString(), $hariLibur);

        if (!$isWeekend && !$isHariLibur) {
            $totalCuti++;
        }

        $current->addDay();
    }

    return response()->json(['total_cuti' => $totalCuti]);
}
      


    public function edit($uuid)
    {
        if (have_permission('cuti_edit')) {
            $cuti = app('GetCutiService')->execute([
                'cuti_uuid' => $uuid
            ]);

            if (empty($cuti['data']))
                return view('errors.404');

            $breadcrumb = [
                ['link' => '/', 'name' => 'Dashboard'],
                ['link' => '/cuti', 'name' => 'Cuti'],
                ['link' => '/cuti/edit', 'name' => 'Edit']
            ];

            return view('cuti.edit', [
                'breadcrumb' => breadcrumb($breadcrumb),
                'roles' => Role::listActiveRole(),
            ]);
        }
        return view('errors.403');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $uuid)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user(); 
            $input_dto = [
                'approve_at' => $request->status == 2 ? now() : null, 
                'approve_by' => $request->status == 2 ? $user->nama : null, 
                'reject_at' => $request->status == 3 ? now() : null, 
                'reject_by' => $request->status == 3 ? $user->nama : null, 
            ];
            app('UpdateCutiService')->execute($input_dto, true);

            $alert = 'success';
            $message = 'Cuti berhasil diupdate';
            DB::commit();
            return redirect()->back()->with($alert, $message);
        } catch (\Exception $ex) {
            DB::rollback();
            $alert = 'danger';
            $message = $ex->getMessage();
            return redirect()->back()->withInput()->with($alert, $message);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */


public function setujui(SetujuiRequest $request, $cuti_uuid)
{
    DB::beginTransaction();
    try {
        // 1. Ambil user approver
        $user = app('GetUserService')->execute([
            'user_uuid' => auth()->user()->uuid,
        ])['data'];

        // 2. Ambil nama penyetuju
        $approver = DB::table('user_informations')
            ->where('user_id', $user->id)
            ->first();

        if (!$approver) {
            throw new \Exception('Data user_informations tidak ditemukan');
        }

        // 3. Status disetujui
        $statusCutiSetuju = StatusCuti::findOrFail(2);

        // 4. Ambil data cuti
        $cuti = DB::table('cutis')->where('uuid', $cuti_uuid)->first();
        if (!$cuti) {
            throw new \Exception('Data cuti tidak ditemukan');
        }

        // 5. Ambil karyawan yang mengajukan
        $karyawan = DB::table('users')->where('id', $cuti->user_id)->first();
        if (!$karyawan) {
            throw new \Exception('Data user tidak ditemukan');
        }

// 6. Hitung total hari cuti (hanya Senin–Jumat)
$tanggalMulai = Carbon::parse($cuti->tanggal_mulai)->startOfDay();
$tanggalAkhir = Carbon::parse($cuti->tanggal_akhir)->startOfDay();

$totalCuti = 0;
for ($date = $tanggalMulai->copy(); $date->lte($tanggalAkhir); $date->addDay()) {
    // Carbon::SUNDAY = 0, Carbon::SATURDAY = 6
    if (! in_array($date->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY])) {
        $totalCuti++;
    }
}

Log::info("Total cuti yang dihitung (tanpa Sabtu & Minggu): {$totalCuti}");

        Log::info("Jenis cuti: {$cuti->jenis_cuti}");

        // 7. Update sisa_cuti hanya jika jenis_cuti = 'tahunan'
        $sisaCutiBaru = $karyawan->sisa_cuti; // default nilai sebelum cek jenis cuti

        if ($cuti->jenis_cuti === 'tahunan') {
            $sisaCutiBaru = max(0, $karyawan->sisa_cuti - $totalCuti);
        
            DB::table('users')
                ->where('id', $karyawan->id)
                ->update(['sisa_cuti' => $sisaCutiBaru]);
        
            Log::info("Kuota cuti karyawan ID {$karyawan->id} dikurangi menjadi {$sisaCutiBaru}");
        } else {
            Log::info("Jenis cuti bukan tahunan ({$cuti->jenis_cuti}), kuota cuti tidak dikurangi.");
        }
        

        // 8. Prepare DTO untuk update status cuti
        $input_dto = [
            'cuti_uuid'       => $cuti_uuid,
            'status_cuti_uuid'=> $statusCutiSetuju->uuid,
            'nama_karyawan'   => $cuti->nama_karyawan,
            'tanggal_mulai'   => $cuti->tanggal_mulai,
            'tanggal_akhir'   => $cuti->tanggal_akhir,
            'keterangan'      => $cuti->keterangan,
            'approve_at'      => now(),
            'approve_by'      => $user->id,
            'sisa_cuti' => $sisaCutiBaru
        ];

        // 9. Update Cuti
        app('UpdateCutiService')->execute($input_dto, true);

        // 10. Kirim notifikasi email ke karyawan
        if (!empty($karyawan->email)) {
            Mail::to($karyawan->email)
                ->send(new CutiApprovalNotification('disetujui', $cuti->nama_karyawan, $approver->nama));
            Log::info("Email notifikasi cuti disetujui terkirim ke: {$karyawan->email}");
        } else {
            Log::warning("Email karyawan tidak ditemukan untuk notifikasi cuti disetujui.");
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Cuti berhasil disetujui dan notifikasi dikirim ke karyawan',
        ], 200);

    } catch (\Exception $ex) {
        DB::rollback();
        Log::error("Gagal proses persetujuan cuti: " . $ex->getMessage());
        return response()->json([
            'success' => false,
            'message' => $ex->getMessage(),
        ], 500);
    }
}




public function tolak(TolakRequest $request, $cuti_uuid)
{
    DB::beginTransaction();
    try {
        $user = app('GetUserService')->execute([
            'user_uuid' => auth()->user()->uuid,
        ])['data'];


                // Ambil nama yang menyetujui dari tabel user_informations
        $approver = DB::table('user_informations')
            ->where('user_id', $user->id)
            ->first();

        if (!$approver) {
            throw new \Exception('Data user_informations tidak ditemukan');
        }

        $status_cuti_tolak = StatusCuti::find(3);

        $input_dto = [
            'cuti_uuid' => $cuti_uuid,
            'status_cuti_uuid' => $status_cuti_tolak->uuid, 
            'nama_karyawan' => $request->nama_karyawan,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_akhir' => $request->tanggal_akhir,
            'keterangan' => $request->keterangan,
            'reject_at' => now(),
            'reject_by' => $user->id,
            
        ];

        app('UpdateCutiService')->execute($input_dto, true);

        // Ambil email karyawan dari tabel cuti
        $cuti = DB::table('cutis')->where('uuid', $cuti_uuid)->first();
        // Ambil email karyawan dari tabel users berdasarkan user_id di tabel cutis
        $karyawan = DB::table('users')->where('id', $cuti->user_id)->first();
                

        if ($karyawan && !empty($karyawan->email)) {
            Mail::to($karyawan->email)->send(new CutiApprovalNotification('ditolak', $cuti->nama_karyawan, $approver->nama));
            Log::info("Email notifikasi cuti disetujui terkirim ke karyawan: " . $karyawan->email);
            Log::info("Nama Karyawan: " . $cuti->nama_karyawan);
            Log::info("Nama Penyetuju: " . $approver->nama);

        } else {
            Log::warning("Email karyawan tidak ditemukan untuk notifikasi cuti yang disetujui.");
        }

        DB::commit();
        return response()->json([
            'success' => true,
            'message' => 'Cuti berhasil ditolak dan notifikasi dikirim ke karyawan'
        ], 200);

    } catch (\Exception $ex) {
        DB::rollback();
        Log::error("Gagal mengirim notifikasi cuti yang ditolak: " . $ex->getMessage());
        return response()->json([
            'success' => false,
            'message' => $ex->getMessage()
        ], 500);
    }
}




    public function download(Request $request, $cuti_uuid)
    {
        try {
            $cuti = app('GetCutiService')->execute([
                'cuti_uuid' => $cuti_uuid
            ])['data'];

            if (isset($cuti->user->userInformation->signatureFile)) {
                $user_signature = $cuti->user->userInformation->signatureFile->generateUrl()->url;
                $relativePathUser = str_replace(url('/storage'), '', $user_signature);
                $user_tanda_tangan = storage_path('app/public' . $relativePathUser);
            } else {
                $user_tanda_tangan = null;
            }

            if (isset($cuti->approveByUser)) {
                $manager_signature = $cuti->approveByUser->userInformation->signatureFile->url ?? null;
                
                if ($manager_signature != null) {
                    $relativePathmanager = str_replace(url('/storage'), '', $manager_signature);
                    $manager_tanda_tangan = storage_path('app/public' . $relativePathmanager);
                } else {
                    $manager_tanda_tangan = null;
                }
            } else if (isset($cuti->rejectByUser)) {
                $manager_signature = $cuti->rejectByUser->userInformation->signatureFile->url ?? null;

                if ($manager_signature != null) {
                    $relativePathmanager = str_replace(url('/storage'), '', $manager_signature);
                    $manager_tanda_tangan = storage_path('app/public' . $relativePathmanager);
                } else {
                    $manager_tanda_tangan = null;
                }
            }

            $approval_by = (isset($cuti->approveByUser)
            ? $cuti->approveByUser->userInformation->nama 
            : $cuti->rejectByUser->userInformation->nama);

            $pdf = PDF::loadView('pdf.cuti', [
                'cuti' => $cuti,
                'approval_by' => $approval_by,
                'user_tanda_tangan' => $user_tanda_tangan,
                'manager_tanda_tangan' => $manager_tanda_tangan
            ]);

            $file_name = "Cuti - " . $cuti->user->userInformation->nama . " - " . $cuti->tanggal_mulai ;

            return $pdf->stream($file_name . ".pdf");
        } catch (\Exception $ex) {
            $message = $ex->getMessage();
            return response()->json([
                'success' => false,
                'message' => $message
            ], 500);
        }
    }

    public function grid(GetCutiRequest $request)
    {
        if (have_permission('cuti_view')) {
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

            $cuti = app('GetCutiService')->execute($request->all());
            // $users = app('GetUserService')->execute([
            //         'role_id_not_in' => [1]
            //     ]);

            return datatables($cuti['data'])->skipPaging()
                ->with([
                    "recordsTotal"    => $cuti['pagination']['total_data'],
                    "recordsFiltered" => $cuti['pagination']['total_data'],
                ])

                ->addColumn('sisa_cuti', function ($row) {
                    // $row->sisa_cuti berasal dari kolom cutis.sisa_cuti
                    return $row->sisa_cuti ?? '-';
                })

                
                ->addColumn('jumlah_cuti', function ($row) {
                    // $row->sisa_cuti berasal dari kolom cutis.sisa_cuti
                    return $row->jumlah_cuti ?? '-';
                })
                
                
                
                ->rawColumns(['action', 'approval', 'tanggal_keputusan', 'pemberi_keputusan'])
                ->editColumn('approval', function ($row){
                    if ($row ->status_cuti_id == 1 && auth()->user()->userRole->role_id !=3) {
                        return "<button value='$row->uuid' class='setujui font-action' style='background-color: green; color: white; display: inline-block; margin-right: 5px; padding: 5px 10px;'><i class='fas fa-check'></i> Setujui</button>"."<button value='$row->uuid' class='tolak font-action' style='background-color: red; color: white; display: inline-block; margin-left: 5px; padding: 5px 10px;'><i class='fas fa-times'></i> Tolak</button>";
                    }
                })
                ->editColumn('tanggal_keputusan', function ($row) {
                    if ($row->status_cuti_id == 2) { 
                        return ($row->approve_at ? $row->approve_at : '-');
                    } elseif ($row->status_cuti_id == 3) { 
                        return ($row->reject_at ? $row->reject_at : '-');
                    }
                    return '-'; 
                })                
                
                ->editColumn('pemberi_keputusan', function ($row) {
                    if ($row->status_cuti_id == 2) { 
                        return $row->approve_by ? optional($row->approveByUser)->userInformation->nama : '-';
                    } elseif ($row->status_cuti_id == 3) { 
                        return $row->reject_by ? optional($row->rejectByUser)->userInformation->nama : '-';
                    }
                    return '-';
                })  
                 
                ->addColumn('action', function ($row) {
                    if (!empty($row->id)) {
                        $action = [];
                        // (have_permission('user_edit')) ? array_push($action, "<a href='" . route('user.edit', [$row->uuid]) . "' class='edit dropdown-item font-action'>Edit</a>") : null;
                        // (have_permission('user_edit')) ? array_push($action, "<a href='" . route('user.edit', [$row->uuid]) . "' class='edit dropdown-item font-action'>Edit</a>") : null;
                        (have_permission('cuti_download') && $row->status_cuti_id == 2 ) ? array_push($action, "<a href='" . route('cuti.download', [$row->uuid]) . "' class='download dropdown-item font-action'>Download</a>") : null;
                        return generate_action_button($action);
                    }
                })

                ->toJson();
        }
        return view('errors.403');
    }
}