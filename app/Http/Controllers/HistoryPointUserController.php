<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\HistoryPointUser;

class HistoryPointUserController extends Controller
{
    public function index()
    {
        // Ambil semua riwayat poin milik user yang sedang login
        $history = HistoryPointUser::where('user_id', Auth::id())
                                   ->orderBy('created_at', 'desc')
                                   ->get();

        return view('history_point_user.index', compact('history'));
    }
}
