<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\UpdatePasswordRequest;
use App\Http\Requests\Api\Auth\UpdateProfileRequest;
use Illuminate\Support\Facades\Hash;
use App\Libraries\Wablas;
use App\Helpers\Generate;
use App\Models\User;

class AuthController extends Controller
{
    public function doLogin (Request $req) {
        $input_dto = [
            'username' => $req->username,
            'password' => $req->password,
        ];

        $do_login  = app('DoLogin')->execute($input_dto);

        return response()->json([
            'success' => ( isset($do_login['error']) ? false : true ),
            'message' => $do_login['message'],
            'data' => $do_login['data'],
        ])->setStatusCode(( isset($do_login['error']) ? 401 : 200 ));
    }

    public function doLogout () {
        $do_logout  = app('DoLogout')->execute([]);

        return response()->json([
            'success' => ( isset($do_login['error']) ? false : true ),
            'message' => $do_logout['message'],
            'data' => $do_logout['data'],
        ]);
    }

    public function getUserSessionInformation () {
        return [
            'uuid' => auth()->user()->uuid,
            'username' => auth()->user()->username,
            'name' => auth()->user()->username,
            'email' => auth()->user()->email,
            'phone_number' => auth()->user()->phone_number,
            'photo_url' => (auth()->user()->photo) ? auth()->user()->photo->generateUrl() : null,
            'role' => auth()->user()->userRole->role
        ];
    }

    public function updateProfile (UpdateProfileRequest $req) {
        $input_dto = $req->all();
        $input_dto['user_uuid'] = auth()->user()->uuid;

        $update_user  = app('UpdateUser')->execute($input_dto, true);

        return response()->json([
            'success' => ( isset($do_login['error']) ? false : true ),
            'message' => $update_user['message'],
            'data' => $update_user['data'],
        ], $update_user['response_code']);
    }

    public function updatePassword (UpdatePasswordRequest $req) {
        if (!Hash::check($req->old_password, auth()->user()->password))
        return response()->json([
            'success' => false,
            'message' => 'Old password is not correct',
        ],400);

        $input_dto = [
            'user_uuid' =>auth()->user()->uuid,
            'new_password' => $req->new_password,
            'new_password_confirmation' => $req->new_password_confirmation
        ];

        $update_user  = app('ChangePassword')->execute($input_dto,true);

        return response()->json([
            'success' => ( isset($do_login['error']) ? false : true ),
            'message' => $update_user['message'],
            'data' => $update_user['data'],
        ], $update_user['response_code']);
    }

    public function resetPassword (Request $req) {
        if (auth()->user()->userRole->role_id != 1)
        return response()->json([
            'success' => false,
            'message' => 'unauthorized',
        ],401);

        $new_password = Generate::generateRandomStringAndNumber(6);
        $user = User::where('uuid', $req->user_uuid)->first();

        $input_dto = [
            'user_uuid' =>$req->user_uuid,
            'new_password' => $new_password,
            'new_password_confirmation' => $new_password
        ];

        $update_user  = app('ChangePassword')->execute($input_dto,true);

        Wablas::sendWhatsApp([
            'phone_number' => $user->phone_number,
            'message' => 'Password akun SMART BMD anda telah direset oleh admin. Password anda sekarang: '.$new_password
        ]);

        return response()->json([
            'success' => ( isset($do_login['error']) ? false : true ),
            'message' => $update_user['message'],
            'data' => $update_user['data'],
        ], $update_user['response_code']);
    }
}
