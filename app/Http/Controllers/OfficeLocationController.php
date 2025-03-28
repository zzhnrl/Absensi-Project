<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OfficeLocation;

class OfficeLocationController extends Controller
{
public function inputLokasi()
{
    $office = \App\Models\OfficeLocation::first();
    return view('input-lokasi', compact('office'));
}

public function cekKehadiran()
{
    $office = \App\Models\OfficeLocation::first();
    return view('deteksi-lokasi', compact('office'));
}
    public function getOfficeLocation()
    {
        $office = \App\Models\OfficeLocation::first(); // Ambil lokasi kantor dari database

        return response()->json([
            'latitude' => $office->latitude,
            'longitude' => $office->longitude
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        // Simpan atau update lokasi kantor di database
        OfficeLocation::updateOrCreate([], [
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return redirect()->route('inputLokasi')->with('success', 'Lokasi kantor berhasil diperbarui!');
    }
}

