@extends('adminlte::page')

@section('title', 'Input Lokasi Kantor')

@section('css')
    <script src="https://cdn.tailwindcss.com"></script>
@endsection

@section('content')
@if(have_permission('dashboard_view'))
<div class="flex justify-center mt-8">
    <div class="bg-white shadow-lg rounded-xl p-8 w-full max-w-3xl">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">📍 Input Maksimal 2 Lokasi Kantor</h2>

        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-4 mb-6 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if($offices->count())
    <div class="mb-6">
        <h3 class="text-lg font-bold text-gray-700 mb-3">📌 Lokasi yang Sudah Disimpan:</h3>
        <ul class="space-y-2">
            @foreach($offices as $office)
                <li class="bg-blue-50 p-4 rounded border-l-4 border-blue-500 text-blue-800">
                    <p><strong>Alamat:</strong> {{ $office->address }}</p>
                    <p><strong>Koordinat:</strong> {{ $office->latitude }}, {{ $office->longitude }}</p>
                </li>
            @endforeach
        </ul>
    </div>
@endif


        <form method="POST" action="{{ route('setOfficeLocation') }}">
            @csrf

            @for($i = 0; $i < 2; $i++)
                <div class="mb-8 border border-gray-200 rounded p-4">
                    <h3 class="text-lg font-bold text-gray-700 mb-2">📍 Lokasi Kantor {{ $i + 1 }}</h3>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Alamat:</label>
                        <input type="text" name="address[]" id="address-{{ $i }}"
                               value="{{ old('address.' . $i, $offices[$i]->address ?? '') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-300 focus:outline-none">
                    </div>

                    <div class="mb-4 flex gap-4">
                        <button type="button" onclick="getCoordinates({{ $i }})"
                                class="flex-1 bg-yellow-400 hover:bg-yellow-500 text-black font-semibold py-2 px-4 rounded transition">
                            🔍 Cari Koordinat
                        </button>
                        <button type="button" onclick="resetLocation({{ $i }})"
                                class="flex-1 bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded transition">
                            🔄 Reset Lokasi
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Latitude:</label>
                            <input type="text" name="latitude[]" id="latitude-{{ $i }}"
                                   value="{{ old('latitude.' . $i, $offices[$i]->latitude ?? '') }}"
                                   class="w-full px-4 py-2 border border-gray-300 bg-gray-100 rounded-md" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Longitude:</label>
                            <input type="text" name="longitude[]" id="longitude-{{ $i }}"
                                   value="{{ old('longitude.' . $i, $offices[$i]->longitude ?? '') }}"
                                   class="w-full px-4 py-2 border border-gray-300 bg-gray-100 rounded-md" readonly>
                        </div>
                    </div>
                </div>
            @endfor

            <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded transition">
                💾 Simpan Lokasi
            </button>
        </form>
    </div>
</div>

<script>
    function getCoordinates(index) {
        const address = document.getElementById(`address-${index}`).value;
        if (!address) return alert("Masukkan alamat terlebih dahulu!");

        const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}&limit=1`;

        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (data.length > 0) {
                    document.getElementById(`latitude-${index}`).value = data[0].lat;
                    document.getElementById(`longitude-${index}`).value = data[0].lon;
                } else {
                    alert("Alamat tidak ditemukan!");
                }
            })
            .catch(() => alert("Gagal mengambil koordinat, coba lagi!"));
    }

    function resetLocation(index) {
        document.getElementById(`address-${index}`).value = "";
        document.getElementById(`latitude-${index}`).value = "";
        document.getElementById(`longitude-${index}`).value = "";
    }
</script>
@endif
@endsection
