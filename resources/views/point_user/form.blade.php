<div class="row">
    <div class="col-12">
        <div class="form-group">
            <label>Tanggal</label>
            <input type="text" class="flatpickr-daterange form-control" placeholder="Pick date range" id="absensi-date" value="{{ Carbon\Carbon::now()->format('Y-m-d') }}" disabled>
        </div>
    
        <div class="form-group">
            <label>Nama</label>
            <input type="text" class="form-control @error('nama_karyawan') is-invalid @enderror" placeholder="Nama Karyawan" name="nama_karyawan" value="{{auth()->user()->userInformation->nama}}" disabled>
            @error('nama_karyawan') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Jumlah Point</label>
            <textarea class="form-control @error('jumlah_point') is-invalid @enderror" placeholder="Masukkan jumlah_point tambahan" name="jumlah_point">{{ old('jumlah_point') }}</textarea>
            @error('jumlah_point') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
    </div>
</div>