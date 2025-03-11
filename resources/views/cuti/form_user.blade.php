<div class="row">
    <div class="col-12">
        <div class="form-group">
            <label>Tanggal Mulai Cuti</label>
            <input type="date" name ="tanggal_mulai" class="flatpickr form-control" placeholder="Masukkan Tanggal Mulai Cuti" value="{{ Carbon\Carbon::now()->format('Y-m-d') }}">
        </div>

        <div class="form-group">
            <label>Tanggal Akhir Cuti</label>
            <input type="date" name ="tanggal_akhir" class="flatpickr form-control" placeholder="Masukkan Tanggal Akhir Cuti" value="{{ Carbon\Carbon::now()->format('Y-m-d') }}">
        </div>
    
        <div class="form-group">
            <label>Nama</label>
            <input type="text" class="form-control @error('nama_karyawan') is-invalid @enderror" placeholder="Nama Karyawan" name="nama_karyawan" value="{{auth()->user()->userInformation->nama}}" disabled>
            @error('nama_karyawan') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Keterangan</label>
            <textarea class="form-control @error('keterangan') is-invalid @enderror" placeholder="Masukkan keterangan tambahan" name="keterangan">{{ old('keterangan') }}</textarea>
            @error('keterangan') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

    </div>
</div>