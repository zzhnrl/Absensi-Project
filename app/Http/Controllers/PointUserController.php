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
use Illuminate\Support\Facades\Log;

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
         if (!have_permission('point_user_view')) {
             return response()->json(['message' => 'Forbidden'], 403);
         }
     
         // Resolve user_uuid dari request jadi user_id
         $userUuid = $request->input('user_uuid');
         if ($userUuid) {
             $user = User::where('uuid', $userUuid)->first();
             $user_ids = $user ? [$user->id] : [];
         } else {
             $approved_role_all = [1, 2];
             $user_ids = in_array(auth()->user()->userRole->role_id, $approved_role_all)
                 ? User::pluck('id')->toArray()
                 : [auth()->user()->id];
         }
     
         // Hitung halaman dan per page dari DataTables param
         $start = $request->input('start', 0);
         $length = $request->input('length', 10);
         $page = intval($start / $length) + 1;
     
         $request->merge([
             'per_page' => $length,
             'page' => $page,
             'with_pagination' => true,
             'search_param' => $request->input('search.value', ''),
             'user_id_in' => $user_ids,
             'month' => $request->input('month'),
             'year' => $request->input('year'),
         ]);
     
         // Panggil service untuk ambil data point user
         $point_user = app('GetPointUserService')->execute($request->all());
     
         $data = $point_user['data'];
     
         // Jika $data masih query builder, jalankan get() untuk ambil data
         if ($data instanceof \Illuminate\Database\Eloquent\Builder || $data instanceof \Illuminate\Database\Query\Builder) {
             $data = $data->get();
         }
     
         $year = $request->input('year');
     
         if ($year) {
             $userIdsInData = collect($data)->pluck('user_id')->unique()->toArray();
     
             // Hitung total point per user di tahun tersebut
             $totalPointByUser = \App\Models\PointUser::whereIn('user_id', $userIdsInData)
                 ->where('tahun', $year)
                 ->select('user_id', DB::raw('SUM(jumlah_point) as total_point'))
                 ->groupBy('user_id')
                 ->pluck('total_point', 'user_id');
     
             $data = collect($data)->map(function ($item) use ($totalPointByUser) {
                 $item['jumlah_point_per_tahun'] = $totalPointByUser[$item['user_id']] ?? 0;
                 return $item;
             })->toArray();
         } else {
             $data = collect($data)->map(function ($item) {
                 $item['jumlah_point_per_tahun'] = 0;
                 return $item;
             })->toArray();
         }
     
         Log::info('Grid Request Params:', $request->all());
     
         // DataTables expects:
         // draw, recordsTotal, recordsFiltered, data[]
         return response()->json([
             'draw' => intval($request->input('draw')),
             'recordsTotal' => $point_user['pagination']['total_data'] ?? count($data),
             'recordsFiltered' => $point_user['pagination']['total_data'] ?? count($data),
             'data' => $data,
         ]);
     }
     
     
}
