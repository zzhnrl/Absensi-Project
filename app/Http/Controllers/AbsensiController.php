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
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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

            if (!$sudahAbsensi) {
                return view('absensi.create', [
                    'breadcrumb' => breadcrumb($breadcrumb),
                    'users' => $users['data'],
                    'kategori_absensis' => KategoriAbsensi::where('is_active', 1)->get()
                ]);
            } else {
                $alert = 'danger';
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
            if (auth()->user()->id == 1){
                $user = app('GetUserService')->execute([
                    'user_uuid' => $request->user_uuid,
                ])['data'];

            } else {
                $user = app('GetUserService')->execute([
                    'user_uuid' => auth()->user()->uuid,
                ])['data'];
            }

            $kategori_absensi = app('GetKategoriAbsensiService')->execute([
                'kategori_absensi_uuid' => $request->kategori_absensi_uuid,
            ])['data'];

            $point_user = app('GetPointUserService')->execute([
                'point_user_uuid' => $request->point_user_uuid,
            ])['data'];

            $bulan = Carbon::parse($request->date)->locale('id')->translatedFormat('F');
            $tahun = Carbon::parse($request->date)->locale('id')->translatedFormat('Y');
            $point_user = PointUser::where('deleted_at', null)
            ->where('nama_karyawan', $user->userInformation->nama) 
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->first();

            if ($point_user) {
                $point_akhir = $point_user->jumlah_point + $kategori_absensi->point;
                app('UpdatePointUserService')->execute([
                    'point_user_id' => $point_user->id,
                    'jumlah_point' => $point_akhir,
                ], true);

            } else { 
                $input_dto = [
                    'user_uuid' => $user->uuid,
                    'nama_karyawan' => $user->userInformation->nama,
                    'bulan' => $bulan, 
                    'tahun' => $tahun,
                    'jumlah_point' => $kategori_absensi->point,
                ];
                app('StorePointUserService')->execute($input_dto, true);
            }

                $input_dto = [
                    'user_uuid' => $user->uuid,
                    'kategori_absensi_uuid' => $request->kategori_absensi_uuid,
                    'nama_karyawan' => $user->userInformation->nama,
                    'nama_kategori' => $kategori_absensi->name,
                    'tanggal' => now(),
                    'keterangan' => $request->keterangan,
                    'jumlah_point' => $kategori_absensi->point,
                ];
        
                $user = app('StoreAbsensiService')->execute($input_dto, true);
        
                $alert = 'success';
                $message = 'Absensi berhasil dibuat';
                DB::commit();
                return redirect()->route('absensi')->with($alert, $message);
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
