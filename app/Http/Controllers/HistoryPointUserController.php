<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\HistoryPointUser;
use Yajra\DataTables\Facades\DataTables;

class HistoryPointUserController extends Controller
{
    public function index()
    {
        // Susun breadcrumb
        $breadcrumb = [
            ['link' => '/',            'name' => 'Dashboard'],
            ['link' => '/history-point','name' => 'Riwayat Poin Saya'],
        ];

        return view('history_point_user.index', [
            'breadcrumb' => breadcrumb($breadcrumb),
        ]);
    }

    

    public function grid(Request $request)
    {
        $query = HistoryPointUser::where('user_id', Auth::id());
    
        if ($request->filled('month') && $request->filled('year')) {
            $query->whereMonth('tanggal', $request->month)
                  ->whereYear('tanggal', $request->year);
        }
    
        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('tanggal', function ($row) {
                return $row->tanggal ? $row->tanggal->format('d/m/Y') : '-';
            })
            ->toJson(); // atau ->make(true);
    }
}
