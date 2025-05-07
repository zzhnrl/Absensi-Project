<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exceptions\CustomException;
use App\Http\Requests\IzinSakit\StoreIzinSakitRequest;
use App\Http\Requests\IzinSakit\GetIzinSakitRequest;
use App\Models\IzinSakit;
use App\Mail\IzinSakitNotification;
use App\Models\RekapIzinSakit;
use App\Models\PointUser;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
                    } else {
                        throw new \Exception("User point not found");
                    }
                }
            }

            $input_dto = [
                'user_uuid' => $user->uuid,
                'nama_karyawan' => $user->userInformation->nama,
                'tanggal' => now(),
                'photo_uuid' => $file_storage['data']['uuid'] ?? null,
                'keterangan' => $request->keterangan,
            ];

            $user = app('StoreIzinSakitService')->execute($input_dto, true);

            
        DB::commit();

        // Kirim email ke semua admin
        $adminUsers = \App\Models\User::where('role_id', 2)->get();

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
        if (have_permission('izin_sakit_view')) {
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

            $izin_sakit = app('GetIzinSakitService')->execute($request->all());

            return datatables($izin_sakit['data'])->skipPaging()
                ->with([
                    "recordsTotal"    => $izin_sakit['pagination']['total_data'],
                    "recordsFiltered" => $izin_sakit['pagination']['total_data'],
                ])
                ->rawColumns(['action', 'pbukti'])
                ->addColumn('pbukti', function ($row) {
                    return (isset($row->photo_id)) ?
                        "<img src='" . $row->photo->generateUrl()->url . "' width='100px' />"
                        :
                        "<img src='img/no_picture.png' width='100px' />";
                })

                ->addColumn('action', function ($row) {
                    if (!empty($row->id)) {
                        $action = [];
                        (have_permission('izin_sakit_delete')) ? array_push($action, "<button value='$row->uuid' class='delete dropdown-item font-action' >Delete</button>") : null;
                        return generate_action_button($action);
                    }
                })
                ->toJson();
        }
        return view('errors.403');
    }
}
