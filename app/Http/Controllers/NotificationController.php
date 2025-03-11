<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index () {
        $breadcrumb = [
            ['link' => '/','name'=>'Dashboard'],
            ['link' => '/user','name'=>'Notifikasi']
        ];

        return view('notifikasi.index', [
            'breadcrumb' => breadcrumb($breadcrumb),
        ]);
    }


    public function readNotification ($notifikasi_uuid) {
        $notification = app('ReadNotifikasi')->execute([
            'notifikasi_uuid' => $notifikasi_uuid
        ]);
        return response()->json([
            'success' => ( isset($notification['error']) ? false : true ),
            'message' => $notification['message'],
            'data' => $notification['data'],
        ]);
    }

    public function listNotification (Request $request) {
         $notification = app('GetNotifikasi')->execute([
            'user_uuid' => auth()->user()->uuid,
            'is_read' => 0,
            'device' => 1
        ]);
        return response()->json([
            'success' => ( isset($notification['error']) ? false : true ),
            'message' => $notification['message'],
            'data' => [
                'count' => count($notification['data']),
                'notification' => $notification['data']
            ],
        ]);
    }

    public function grid(Request $request)
    {
        if (have_permission('role_view')) {

            $request->merge([
                'per_page' => $request->length,
                'page' => $request->start/$request->length + 1,
                'user_uuid' => auth()->user()->uuid,
                'with_pagination' => true,
                'search_param' => $request->search['value'] ?? null,
                'device' => 1,
                'is_read' => false
            ]);

            $role = app('GetNotifikasi')->execute($request->all());
            return datatables($role['data'])->skipPaging()
            ->with(["recordsTotal"    => $role['pagination']['total_data'],
            "recordsFiltered" => $role['pagination']['total_data'],
            ])
            ->rawColumns(['action','teks'])
            ->addColumn('action', function ($row) {
                    $action = [];
                    array_push($action, "<button value='$row->uuid' class='delete dropdown-item mark-as-read' >Mark as read</button>");
                    return generate_action_button($action);
            })
            ->toJson();
        }

        return response()->json([
            'success' => false
        ],403);
    }

}
