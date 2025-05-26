@extends('adminlte::page')

@section('title', 'Riwayat Poin Saya')

@section('content_header')
    <h1>Riwayat Poin Saya</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>Perubahan Poin</th>
                    <th>Total Poin</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($history as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->perubahan_point }}</td>
                    <td>{{ $item->jumlah_point }}</td>
                    <td>
    {{ $item->tanggal
        ? $item->tanggal->format('d/m/Y')
        : '-' 
    }}
</td>


                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center">Belum ada riwayat poin</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@stop
