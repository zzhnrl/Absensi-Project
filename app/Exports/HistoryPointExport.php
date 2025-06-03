<?php

namespace App\Exports;

use App\Models\HistoryPointUser;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromView;

class HistoryPointExport implements FromView
{
    protected $month, $year;

    public function __construct($month, $year)
    {
        $this->month = $month;
        $this->year = $year;
    }

    public function view(): View
    {
        $data = HistoryPointUser::where('user_id', Auth::id())
            ->when($this->month, fn($q) => $q->whereMonth('tanggal', $this->month))
            ->when($this->year, fn($q) => $q->whereYear('tanggal', $this->year))
            ->get();

        return view('exports.history-point', [
            'data' => $data
        ]);
    }
}
