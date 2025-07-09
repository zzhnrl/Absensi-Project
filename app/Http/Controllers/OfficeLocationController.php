<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OfficeLocation;

class OfficeLocationController extends Controller
{
public function inputLokasi()
{
    $offices = OfficeLocation::orderBy('index')->limit(2)->get();
    return view('input-lokasi', compact('offices'));
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
            'address'     => 'required|array|max:2',
            'address.*'   => 'required|string|max:255',
            'latitude'    => 'required|array',
            'latitude.*'  => 'required|numeric',
            'longitude'   => 'required|array',
            'longitude.*' => 'required|numeric',
        ]);

        foreach ($request->address as $i => $address) {
            OfficeLocation::updateOrCreate(
                ['index' => $i], // index sebagai pembeda
                [
                    'address' => $address,
                    'latitude' => $request->latitude[$i],
                    'longitude' => $request->longitude[$i],
                ]
            );
        }

        return redirect()->route('inputLokasi')->with('success', 'Maksimal dua lokasi kantor berhasil disimpan!');
    }
}

