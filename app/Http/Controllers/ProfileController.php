<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomException;
use App\Http\Requests\Profile\UpdateProfileRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index () {
        $breadcrumb = [
            ['link' => '/','name'=>'Dashboard'],
            ['link' => '/profile','name'=>'Profile']
        ];

        $user = app('GetUser')->execute([
            'user_uuid' => auth()->user()->uuid
        ]);
        return view('profile.index', [
            'breadcrumb' => breadcrumb($breadcrumb),
            'user' => $user['data']
        ]);
    }

    public function update (UpdateProfileRequest $request, $uuid) {
        DB::beginTransaction();
        try {

            if (isset($request->old_password) && !Hash::check($request->old_password, auth()->user()->password))
            throw new CustomException("Old password is not correct", 400);

            if ($request->hasFile('image')) {
                $file_storage = app('StoreFileStorage')->execute([
                    'file' => $request->file('image'),
                    'location' => 'image/'. now()->format('Y-m-d'),
                    'filesystem' => 'public',
                    'compress' => false
                ],true);
            }

            $input_dto = [
                'user_uuid' => auth()->user()->uuid,
                'photo_uuid' => $file_storage['data']['uuid'] ?? null,
                'username' => $request->username,
                'nama' => $request->nama,
                'email' => $request->email,
                'nomor_telepon' => $request->nomor_telepon,
                'password' => $request->new_password ?? null
            ];
            app('UpdateUser')->execute($input_dto,true);

            $alert = 'success';
            $message = 'User berhasil diupdate';
            DB::commit();
            return redirect()->back()->with($alert,$message);
        }catch (\Exception $ex) {
            DB::rollback();
            $alert = 'danger';
            $message = $ex->getMessage();
            return redirect()->back()->withInput()->with($alert,$message);
        }
    }
}
