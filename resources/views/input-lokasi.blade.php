@extends('adminlte::page')

@section('title', 'Input Lokasi Kantor')

@section('css')
    <script src="https://cdn.tailwindcss.com"></script>
@endsection

@section('content_header')
    @if(have_permission('dashboard_view'))
        <h1 class="text-light">Input Lokasi Kantor</h1>
    @endif
@endsection

@section('content')
@if(have_permission('dashboard_view'))
<div class="flex justify-center mt-8">
    <div class="bg-white shadow-lg rounded-xl p-8 w-full max-w-xl">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">📍 Input Lokasi Kantor</h2>

        {{-- Menampilkan informasi jika lokasi sudah pernah diinput --}}
        @if(isset($office))
            <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-6 rounded" role="alert">
                <p class="font-bold">Lokasi kantor saat ini:</p>
                <p>📍 <strong>Alamat:</strong> {{ $office->address }}</p>
                <p>🌐 <strong>Koordinat:</strong> {{ $office->latitude }}, {{ $office->longitude }}</p>
            </div>
        @endif

        {{-- Form Input --}}
        <form method="POST" action="{{ route('setOfficeLocation') }}" onsubmit="return validateForm()">
            @csrf

            <div class="mb-4">
                <label for="address" class="block text-sm font-semibold text-gray-700 mb-1">Alamat Kantor:</label>
                <input type="text" id="address" name="address" placeholder="Masukkan alamat..."
                       value="{{ old('address', $office->address ?? '') }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-300 focus:outline-none" required>
            </div>

            <div class="mb-4 flex gap-4">
                <button type="button" onclick="getCoordinates()"
                        class="flex-1 bg-yellow-400 hover:bg-yellow-500 text-black font-semibold py-2 px-4 rounded transition">
                    🔍 Cari Koordinat
                </button>
                <button type="button" onclick="resetLocation()"
                        class="flex-1 bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded transition">
                    🔄 Reset Lokasi
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label for="latitude" class="block text-sm font-semibold text-gray-700 mb-1">Latitude:</label>
                    <input type="text" id="latitude" name="latitude" readonly
                           value="{{ old('latitude', $office->latitude ?? '') }}"
                           class="w-full px-4 py-2 border border-gray-300 bg-gray-100 rounded-md focus:outline-none" required>
                </div>
                <div>
                    <label for="longitude" class="block text-sm font-semibold text-gray-700 mb-1">Longitude:</label>
                    <input type="text" id="longitude" name="longitude" readonly
                           value="{{ old('longitude', $office->longitude ?? '') }}"
                           class="w-full px-4 py-2 border border-gray-300 bg-gray-100 rounded-md focus:outline-none" required>
                </div>
            </div>

            <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded transition">
                💾 Simpan Lokasi
            </button>
        </form>
    </div>
</div>

{{-- Script untuk mencari koordinat dan reset lokasi --}}
<script>
    function getCoordinates() {
        const address = document.getElementById("address").value;
        if (!address) return alert("Masukkan alamat terlebih dahulu!");

        const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}&limit=1`;

        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (data.length > 0) {
                    document.getElementById("latitude").value = data[0].lat;
                    document.getElementById("longitude").value = data[0].lon;
                } else {
                    alert("Alamat tidak ditemukan!");
                }
            })
            .catch(() => alert("Gagal mengambil koordinat, coba lagi!"));
    }

    function resetLocation() {
        document.getElementById("address").value = "";
        document.getElementById("latitude").value = "";
        document.getElementById("longitude").value = "";
    }
</script>

@endif
@endsection
