<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exceptions\CustomException;
use App\Http\Requests\Cuti\StoreCutiRequest;
use App\Http\Requests\Cuti\GetCutiRequest;
use App\Http\Requests\Cuti\SetujuiRequest;
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

class CutiController extends Controller
{
    public function index()
    {
        $breadcrumb = [
            ['link' => '/', 'name' => 'Dashboard'],
            ['link' => '/cuti', 'name' => 'Cuti']
        ];

        $users = app('GetUserService')->execute([
            'role_id_not_in' => [1]
        ]);

        $status_cutis = app('GetStatusCutiService')->execute([]);

        return view('cuti.index', [
            'breadcrumb' => breadcrumb($breadcrumb),
            'users' => $users['data'],
            'status_cutis' => $status_cutis['data']
        ]);

        return view('errors.403');
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

            $input_dto = [
                'status_cuti_id' => 1,
                'user_uuid' => $user->uuid,
                'nama_karyawan' => $user->userInformation->nama,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_akhir' => $request->tanggal_akhir,
                'perihal' => $request->perihal,
                'keterangan' => $request->keterangan,
            ];

            $user = app('StoreCutiService')->execute($input_dto, true);

            $alert = 'success';
            $message = 'Cuti berhasil diajukan';
            DB::commit();
            return redirect()->route('cuti')->with($alert, $message);
        } catch (\Exception $ex) {
            DB::rollback();
            $alert = 'danger';
            $message = $ex->getMessage();
            return redirect()->back()->withInput()->with($alert, $message);
        }
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

    public function setujui(Request $request, $cuti_uuid)
    {
        DB::beginTransaction();
        try {
            $user = app('GetUserService')->execute([
                'user_uuid' => auth()->user()->uuid,
            ])['data'];

            $status_cuti_setuju = StatusCuti::find(2);

            $input_dto = [
                'cuti_uuid' => $cuti_uuid,
                'status_cuti_uuid' => $status_cuti_setuju->uuid, 
                'nama_karyawan' => $request->nama_karyawan,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_akhir' => $request->tanggal_akhir,
                'perihal' => $request->perihal,
                'keterangan' => $request->keterangan,
                'approve_at' => now(),
                'approve_by' => $user->id,
            ];

            app('UpdateCutiService')->execute($input_dto, true);
            
            DB::commit();
            $message = 'Gagal menyetujui cuti';
            return response()->json([
                'success' => false,
                'message' => $message
            ],200);

        }catch (\Exception $ex) {
            DB::rollback();
            $message = $ex->getMessage();
            return response()->json([
                'success' => false,
                'message' => $message
            ],500);
        }
    }

    public function tolak(Request $request, $cuti_uuid)
    {
        DB::beginTransaction();
        try {
            $user = app('GetUserService')->execute([
                'user_uuid' => auth()->user()->uuid,
            ])['data'];
            
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
            DB::commit();

            $message = 'Gagal menolak cuti';
            return response()->json([
                'success' => false,
                'message' => $message
            ],200);

        }catch (\Exception $ex) {
            DB::rollback();
            $message = $ex->getMessage();
            return response()->json([
                'success' => false,
                'message' => $message
            ],500);
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