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
use Illuminate\Support\Facades\DB;

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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
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
                'email' => $request->email,
                'password' => $request->password,
                'password_confirmation' => $request->password_confirmation,
                'role_uuid' => $request->role,
                'signature_file_uuid' => $file_storage_ttd['data']['uuid'] ?? null,
                'nama' => $request->nama,
                'notlp' => $request->notlp,
                'alamat' => $request->alamat,
            ];

            $user = app('RegisterNewUserService')->execute($input_dto, true);

            $alert = 'success';
            $message = 'User berhasil dibuat, password ' . $input_dto['password'];
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
     * @return \Illuminate\Http\Response
     */
    public function grid(GetUserRequest $request)
    {
        if (have_permission('user_view')) {
            $request->merge([
                'per_page' => $request->length,
                'page' => $request->start / $request->length + 1,
                'with_pagination' => true,
                'search_param' => $request->search['value']
            ]);

            $user = app('GetUserService')->execute($request->all());

            return datatables($user['data'])->skipPaging()
                ->with([
                    "recordsTotal"    => $user['pagination']['total_data'],
                    "recordsFiltered" => $user['pagination']['total_data'],
                ])
                ->rawColumns(['action', 'profile_picture', 'signature_file'])

                ->addColumn('profile_picture', function ($row) {
                    return (isset($row->photo_id)) ?
                        "<img src='" . $row->photo->generateUrl()->url . "' width='100px' />"
                        :
                        "<img src='img/no_picture.png' width='100px' />";
                })
                ->addColumn('signature_file', function ($row) {
                    return (isset($row->userInformation->signature_file_id)) ?
                        "<img src='" . $row->userInformation->signatureFile->generateUrl()->url . "' width='100px' />"
                        :
                        "<img src='img/no_picture.png' width='100px' />";
                })
                ->addColumn('action', function ($row) {
                    if (!in_array($row->userRole->role_id, [1])) {
                        $action = [];
                        (have_permission('user_edit')) ? array_push($action, "<a href='" . route('user.edit', [$row->uuid]) . "' class='edit dropdown-item font-action'>Edit</a>") : null;
                        (have_permission('user_delete')) ? array_push($action, "<button value='$row->uuid' class='delete dropdown-item font-action' >Delete</button>") : null;
                        return generate_action_button($action);
                    }
                })
                ->toJson();
        }
        return view('errors.403');
    }
}
