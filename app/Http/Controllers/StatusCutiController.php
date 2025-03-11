<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StatusCuti\GetStatusCutiRequest;
use App\Http\Requests\StatusCuti\StoreStatusCutiRequest;
use Illuminate\Support\Facades\DB;

class StatusCutiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (have_permission('status_cuti_view')) {
            $breadcrumb = [
                ['link' => '/', 'name' => 'Dashboard'],
                ['link' => '/status_cuti', 'name' => 'Status Cuti']
            ];
            return view('status_cuti.index', [
                'breadcrumb' => breadcrumb($breadcrumb)
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
        if (have_permission('status_cuti_create')) {
            $breadcrumb = [
                ['link' => '/', 'name' => 'Dashboard'],
                ['link' => '/status_cuti', 'name' => 'Status Cuti'],
                ['link' => '/status_cuti/create', 'name' => 'Create']
            ];
            return view('status_cuti.create', [
                'breadcrumb' => breadcrumb($breadcrumb),
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
        if (have_permission('status_cuti_create')) {
            $result = app('StoreStatusCutiService')->execute([
                'nama' => $request->nama,
                'kode' => $request->kode,
                'deskripsi' => $request->deskripsi,
            ]);
            $alert = (isset($result['error'])) ? 'danger' : 'success';
            if ($result['error'] != null) {
                if ($result['response_code'] == 422) {
                    $error = \Illuminate\Validation\ValidationException::withMessages(collect($result['message'])->toArray());
                    throw $error;
                } else {
                    return redirect()->back()->withInput()->with($alert, $result['message']);
                }
            }
            return redirect()->back()->with($alert, $result['message']);
        }
        return view('errors.403');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($uuid)
    {
        if (have_permission('status_cuti_edit')) {
            $status_cuti = app('GetStatusCutiService')->execute([
                'status_cuti_uuid' => $uuid
            ]);

            if (empty($status_cuti['data']))
                return view('errors.404');

            $breadcrumb = [
                ['link' => '/', 'name' => 'Dashboard'],
                ['link' => '/status_cuti', 'name' => 'Status Cuti'],
                ['link' => '/status_cuti/edit', 'name' => 'Edit']
            ];
            return view('status_cuti.edit', [
                'breadcrumb' => breadcrumb($breadcrumb),
                'status_cuti' => $status_cuti['data']
            ]);
        }
        return view('errors.403');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $uuid)
    {
        if (have_permission('status_cuti_edit')) {
            $result = app('UpdateStatusCutiService')->execute([
                'status_cuti_uuid' => $uuid,
                'nama' => $request->nama,
                'kode' => $request->kode,
                'deskripsi' => $request->deskripsi
            ]);

            $alert = (isset($result['error'])) ? 'danger' : 'success';
            if ($result['error'] != null) {
                if ($result['response_code'] == 422) {
                    $error = \Illuminate\Validation\ValidationException::withMessages(collect($result['message'])->toArray());
                    throw $error;
                } else {
                    return redirect()->back()->withInput()->with($alert, $result['message']);
                }
            }
            return redirect()->back()->with($alert, $result['message']);
        }
        return view('errors.403');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($uuid)
    {
        if (have_permission('status_cuti_delete')) {
            DB::beginTransaction();
            try {
                $input_dto = [
                    'status_cuti_uuid' => $uuid
                ];
                app('DeleteStatusCutiService')->execute($input_dto, true);
                DB::commit();
                $message = 'Status cuti berhasil dihapus';
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
    public function grid(Request $request)
    {
        if (have_permission('status_cuti_view')) {

            $request->merge([
                'per_page' => $request->length,
                'page' => $request->start / $request->length + 1,
                'with_pagination' => true,
                'search_param' => $request->search['value'] ?? null
            ]);

            $status_cuti = app('GetStatusCutiService')->execute($request->all());
            return datatables($status_cuti['data'])->skipPaging()
                ->with([
                    "recordsTotal"    => $status_cuti['pagination']['total_data'],
                    "recordsFiltered" => $status_cuti['pagination']['total_data'],
                ])
                ->rawColumns(['action'])
                ->addColumn('action', function ($row) {
                    if (!in_array($row->id, [1])) {
                        $action = [];
                        (have_permission('status_cuti_edit')) ? array_push($action, "<a href='" . route('status_cuti.edit', [$row->uuid]) . "' class='edit dropdown-item font-action'>Edit</a>") : null;
                        (have_permission('status_cuti_delete')) ? array_push($action, "<button value='$row->uuid' class='delete dropdown-item font-action' >Delete</button>") : null;
                        return generate_action_button($action);
                    }
                })
                ->toJson();
        }

        return response()->json([
            'success' => false
        ], 403);
    }
}
