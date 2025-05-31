<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

function hitungHariCuti($tanggal_mulai, $tanggal_selesai)
{
    $response = Http::get('https://api-harilibur.vercel.app/api');

    if (!$response->successful()) {
        throw new \Exception('Gagal mengambil data hari libur nasional.');
    }

    // Ambil tanggal libur, pastikan format YYYY-MM-DD
    $hari_libur = collect($response->json())
        ->map(fn($item) => Carbon::parse($item['holiday_date'])->toDateString())
        ->toArray();

    $mulai = Carbon::parse($tanggal_mulai);
    $selesai = Carbon::parse($tanggal_selesai);
    $jumlah_hari = 0;

    while ($mulai <= $selesai) {
        $isWeekend = $mulai->isWeekend();
        $tanggalStr = $mulai->toDateString();
        $isHariLibur = in_array($tanggalStr, $hari_libur);

        // Debug
        // info("Tanggal: $tanggalStr, Weekend: $isWeekend, Hari Libur: $isHariLibur");

        if (!$isWeekend && !$isHariLibur) {
            $jumlah_hari++;
        }
        $mulai->addDay();
    }

    return $jumlah_hari;

}
