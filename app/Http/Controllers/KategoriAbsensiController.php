<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\KategoriAbsensi\StoreKategoriAbsensiRequest;
use Illuminate\Support\Facades\DB;

class KategoriAbsensiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (have_permission('kategori_absensi_view')) {
            $breadcrumb = [
                ['link' => '/', 'name' => 'Dashboard'],
                ['link' => '/kategori_absensi', 'name' => 'Kategori Absensi']
            ];
            return view('kategori_absensi.index', [
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
        if (have_permission('kategori_absensi_create')) {
            $breadcrumb = [
                ['link' => '/', 'name' => 'Dashboard'],
                ['link' => '/kategori_absensi', 'name' => 'Kategori Absen'],
                ['link' => '/kategori_absensi/create', 'name' => 'Create']
            ];
            return view('kategori_absensi.create', [
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
        if (have_permission('kategori_absensi_create')) {
            $result = app('StoreKategoriAbsensiService')->execute([
                'name' => $request->name,
                'code' => $request->code,
                'point' => $request->point,
                'description' => $request->description,
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
            return redirect()->route('kategori_absensi')->with($alert, $result['message']);
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
        if (have_permission('kategori_absensi_edit')) {
            $kategori_absensi = app('GetKategoriAbsensiService')->execute([
                'kategori_absensi_uuid' => $uuid
            ]);

            if (empty($kategori_absensi['data']))
                return view('errors.404');

            $breadcrumb = [
                ['link' => '/', 'name' => 'Dashboard'],
                ['link' => '/kategori_absensi', 'name' => 'Kategori Absen'],
                ['link' => '/kategori_absensi/edit', 'name' => 'Edit']
            ];
            return view('kategori_absensi.edit', [
                'breadcrumb' => breadcrumb($breadcrumb),
                'kategori_absensi' => $kategori_absensi['data']
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
        if (have_permission('kategori_absensi_edit')) {
            $result = app('UpdateKategoriAbsensiService')->execute([
                'kategori_absensi_uuid' => $uuid,
                'name' => $request->name,
                'code' => $request->code,
                'point' => $request->point,
                'description' => $request->description
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
            return redirect()->route('kategori_absensi')->with($alert, $result['message']);
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
        if (have_permission('kategori_absensi_delete')) {
            DB::beginTransaction();
            try {
                $input_dto = [
                    'kategori_absensi_uuid' => $uuid
                ];
                app('DeleteKategoriAbsensiService')->execute($input_dto, true);
                DB::commit();
                $message = 'Kategori absensi berhasil dihapus';
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
        if (have_permission('kategori_absensi_view')) {

            $request->merge([
                'per_page' => $request->length,
                'page' => $request->start / $request->length + 1,
                'with_pagination' => true,
                'search_param' => $request->search['value'] ?? null
            ]);

            $kategori_absensi = app('GetKategoriAbsensiService')->execute($request->all());
            return datatables($kategori_absensi['data'])->skipPaging()
                ->with([
                    "recordsTotal"    => $kategori_absensi['pagination']['total_data'],
                    "recordsFiltered" => $kategori_absensi['pagination']['total_data'],
                ])
                ->rawColumns(['action'])
                ->addColumn('action', function ($row) {
                    if (!empty($row->id)) {
                        $action = [];
                        (have_permission('kategori_absensi_edit')) ? array_push($action, "<a href='".route('kategori_absensi.edit', [$row->uuid])."' class='edit dropdown-item font-action'>Edit</a>") : null;
                        (have_permission('kategori_absensi_delete')) ? array_push($action, "<button value='$row->uuid' class='delete dropdown-item font-action' >Delete</button>") : null;
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
