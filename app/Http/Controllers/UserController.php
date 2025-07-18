<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\DeleteUserRequest;
use App\Http\Requests\User\GetUserRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use App\Models\UserInformation;
use Illuminate\Http\Request;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (have_permission('user_view')) {
            $breadcrumb = [
                ['link' => '/', 'name' => 'Dashboard'],
                ['link' => '/user', 'name' => 'User']
            ];

            // $roles = app('GetRoleService')->execute([]);

            return view('user.index', [
                'breadcrumb' => breadcrumb($breadcrumb),
                'roles' => Role::listActiveRole(),
            ]);
        }
        return view('errors.403');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (have_permission('user_create')) {
            $breadcrumb = [
                ['link' => '/', 'name' => 'Dashboard'],
                ['link' => '/user', 'name' => 'User'],
                ['link' => '/user/create', 'name' => 'Create']
            ];

            return view('user.create', [
                'breadcrumb' => breadcrumb($breadcrumb),
                'roles' => Role::listActiveRole(),
            ]);
        }
        return view('errors.403');
    }


public function getSisaCuti($id)
{
    $user = User::where('uuid', $id)->first();

    if (!$user) {
        return response()->json(['error' => 'User tidak ditemukan'], 404);
    }

    return response()->json(['sisa_cuti' => $user->sisa_cuti]);
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
        // Log data yang diterima dari request
        Log::info('Data Request:', $request->all());

        // Simpan Foto Profil
        $file_storage = null;
        if ($request->hasFile('image')) {
            $file_storage = app('StoreFileStorageService')->execute([
                'file' => $request->file('image'),
                'location' => 'image/' . now()->format('Y-m-d'),
                'filesystem' => 'public',
                'compress' => false
            ], true);
        }

        // Simpan Tanda Tangan
        $file_storage_ttd = null;
        if ($request->hasFile('images')) {
            $file_storage_ttd = app('StoreFileStorageService')->execute([
                'file' => $request->file('images'),
                'location' => 'images/' . now()->format('Y-m-d'),
                'filesystem' => 'public',
                'compress' => false
            ], true);
        }

        // Cek apakah sisa_cuti dikirim dari form
        if ($request->filled('sisa_cuti')) {
            $sisa_cuti = $request->sisa_cuti;
        } else {
            $sisa_cuti = null; // Default menjadi null jika tidak diisi
        }

        // Tentukan role_id berdasarkan role yang dipilih
        $role_id = null;
        if ($request->role == 'Karyawan') {
            $role_id = 3;
        } elseif ($request->role == 'Manajer') {
            $role_id = 2;
        }

        // Log nilai sisa_cuti dan role_id sebelum dikirim ke database
        Log::info('Nilai sisa_cuti dan role_id setelah diolah:', [
            'sisa_cuti' => $sisa_cuti,
            'role_id' => $role_id
        ]);

        // Data untuk penyimpanan user baru
        $input_dto = [
            'photo_uuid' => $file_storage['data']['uuid'] ?? null,
            'email' => $request->email,
            'password' => $request->password,
            'password_confirmation' => $request->password_confirmation,
            'role_id' => $role_id, // Menyimpan role_id berdasarkan pilihan role
            'signature_file_uuid' => $file_storage_ttd['data']['uuid'] ?? null,
            'nama' => $request->nama,
            'role_uuid' => $request->role,
            'notlp' => $request->notlp,
            'alamat' => $request->alamat,
            'sisa_cuti' => $sisa_cuti, // Pastikan sisa_cuti tetap sesuai dengan yang diinput
        ];

        // Log data sebelum disimpan ke database
        Log::info('Data yang akan disimpan ke database:', $input_dto);

        // Simpan data user
        $user = app('RegisterNewUserService')->execute($input_dto, true);

        DB::commit();
        return redirect()->route('user')->with('success', 'User berhasil dibuat, password ' . $input_dto['password']);
    } catch (\Exception $ex) {
        DB::rollback();
        Log::error('Terjadi kesalahan saat menyimpan user:', ['error' => $ex->getMessage()]);
        return redirect()->back()->withInput()->with('danger', $ex->getMessage());
    }
}



    /**
     * Show the form for editing the specified resource.
     *
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */
    public function edit($uuid)
    {
        if (have_permission('user_edit')) {
            $user = app('GetUserService')->execute([
                'user_uuid' => $uuid
            ]);

            $user_informations = app('GetUserInformationService')->execute([
                'user_id' => $user['data']->id
            ]);

            if (empty($user['data']))
                return view('errors.404');

            $breadcrumb = [
                ['link' => '/', 'name' => 'Dashboard'],
                ['link' => '/user', 'name' => 'User'],
                ['link' => '/user/edit', 'name' => 'Edit']
            ];

            return view('user.edit', [
                'breadcrumb' => breadcrumb($breadcrumb),
                'roles' => Role::listActiveRole(),
                'user' => $user['data'],
                'user_information' => $user_informations['data']->first(),
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
    public function update(UpdateUserRequest $request, $uuid)
    {
        DB::beginTransaction();
        try {
            if ($request->hasFile('image')) {
                $file_storage = app('StoreFileStorageService')->execute([
                    'file' => $request->file('image'),
                    'location' => 'image/' . now()->format('Y-m-d'),
                    'filesystem' => 'public',
                    'compress' => false
                ], true);
            }

            if ($request->hasFile('images')) {
                $file_storage_ttd = app('StoreFileStorageService')->execute([
                    'file' => $request->file('images'),
                    'location' => 'images/' . now()->format('Y-m-d'),
                    'filesystem' => 'public',
                    'compress' => false
                ], true);
            }

            $input_dto = [
                'photo_uuid' => $file_storage['data']['uuid'] ?? null,
                'signature_file_uuid' => $file_storage_ttd['data']['uuid'] ?? null,
                'user_uuid' => $uuid,
                'role_uuid' => $request->role,
                'email' => $request->email,
                'password' => $request->password,
                'password_confirmation' => $request->password_confirmation,
                'remove_picture' => $request->remove_picture,

                'user_information_uuid' => $request->user_information,
                'nama' => $request->nama,
                'notlp' => $request->notlp,
                'alamat' => $request->alamat,
            ];
            app('EditUserService')->execute($input_dto, true);

            $alert = 'success';
            $message = 'User berhasil diupdate';
            DB::commit();
            return redirect()->route('user')->with($alert, $message);
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
    public function destroy($uuid)
    {
        if (have_permission('user_delete')) {
            DB::beginTransaction();
            try {
                $user = User::where('uuid', $uuid)->first();

                $input_dto = [
                    'user_uuid' => $uuid,
                    'user_information_uuid' => $user->userInformation->$uuid,
                ];

                app('RemoveUserService')->execute($input_dto, true);
                DB::commit();

                $message = 'User berhasil dihapus';
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

    /**
     * Display a listing of the resource in datatable formats.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function grid(GetUserRequest $request)
    {
        if (have_permission('user_view')) {
            Log::info('Filter role_uuid: ', ['role_uuid' => $request->role_uuid]); // Untuk debugging
    
            $request->merge([
                'per_page' => $request->length,
                'page' => $request->start / $request->length + 1,
                'with_pagination' => true,
                'search_param' => $request->search['value'] ?? null
            ]);
    
            $user = app('GetUserService')->execute($request->all());
    
            return datatables($user['data'])->skipPaging()
                ->with([
                    "recordsTotal"    => $user['pagination']['total_data'],
                    "recordsFiltered" => $user['pagination']['total_data'],
                ])
                ->rawColumns(['action', 'profile_picture', 'signature_file'])
                ->addColumn('profile_picture', function ($row) {
                    return $row->photo_id
                        ? "<img src='" . $row->photo->generateUrl()->url . "' width='100px' />"
                        : "<img src='img/no_picture.png' width='100px' />";
                })
                ->addColumn('signature_file', function ($row) {
                    return isset($row->userInformation->signature_file_id)
                        ? "<img src='" . $row->userInformation->signatureFile->generateUrl()->url . "' width='100px' />"
                        : "<img src='img/no_picture.png' width='100px' />";
                })
                ->addColumn('action', function ($row) {
                    if (!in_array($row->userRole->role_id, [1])) {
                        $action = [];
                        if (have_permission('user_edit')) {
                            $action[] = "<a href='" . route('user.edit', [$row->uuid]) . "' class='edit dropdown-item font-action'>Edit</a>";
                        }
                        if (have_permission('user_delete')) {
                            $action[] = "<button value='$row->uuid' class='delete dropdown-item font-action'>Delete</button>";
                        }
                        return generate_action_button($action);
                    }
                })
                ->addColumn('password', function ($row) {
                    try {
                        return Crypt::decryptString($row->password);
                    } catch (\Exception $e) {
                        return '—';
                    }
                })
                ->toJson();
        }
    
        return view('errors.403');
    }
    
}