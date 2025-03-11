<?php

namespace App\Http\Controllers;

use App\Http\Requests\RekapIzinSakit\DeleteRekapIzinSakitRequest;
use App\Http\Requests\RekapIzinSakit\GetRekapIzinSakitRequest;
use App\Http\Requests\RekapIzinSakit\StoreRekapIzinSakitRequest;
use App\Http\Requests\RekapIzinSakit\UpdateRekapIzinSakitRequest;
use App\Models\RekapIzinSakit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RekapIzinSakitController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (have_permission('rekap_izin_sakit_view')) {
            $breadcrumb = [
                ['link' => '/', 'name' => 'Dashboard'],
                ['link' => '/rekap_izin_sakit', 'name' => 'Rekap Izin Sakit']
            ];

            $users = app('GetUserService')->execute([
                'role_id_not_in' => [1]
            ]);

            return view('rekap_izin_sakit.index', [
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
    
    public function grid(GetRekapIzinSakitRequest $request)
    {
        if (have_permission('rekap_izin_sakit_view')) {
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

            $rekap_izin_sakit = app('GetRekapIzinSakitService')->execute($request->all());

            return datatables($rekap_izin_sakit['data'])->skipPaging()
                ->with([
                    "recordsTotal"    => $rekap_izin_sakit['pagination']['total_data'],
                    "recordsFiltered" => $rekap_izin_sakit['pagination']['total_data'],
                ])
                ->toJson();
        }
        return view('errors.403');
    }
}
