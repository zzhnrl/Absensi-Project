<?php

namespace App\Http\Controllers;

use App\Http\Requests\PointUser\DeletePointUserRequest;
use App\Http\Requests\PointUser\GetPointUserRequest;
use App\Http\Requests\PointUser\StorePointUserRequest;
use App\Http\Requests\PointUser\UpdatePointUserRequest;
use App\Models\PointUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PointUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (have_permission('point_user_view')) {
            $breadcrumb = [
                ['link' => '/', 'name' => 'Dashboard'],
                ['link' => '/point_user', 'name' => 'Point User']
            ];

            $users = app('GetUserService')->execute([
                'role_id_not_in' => [1]
            ]);

            return view('point_user.index', [
                'breadcrumb' => breadcrumb($breadcrumb),
                'users' => $users['data']
            ]);
        }
        return view('errors.403');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function grid(GetPointUserRequest $request)
    {
        if (have_permission('point_user_view')) {
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

            $point_user = app('GetPointUserService')->execute($request->all());

            return datatables($point_user['data'])->skipPaging()
                ->with([
                    "recordsTotal"    => $point_user['pagination']['total_data'],
                    "recordsFiltered" => $point_user['pagination']['total_data'],
                ])
                ->toJson();
        }
        return view('errors.403');
    }
}
