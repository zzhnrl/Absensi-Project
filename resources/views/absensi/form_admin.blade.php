<div class="row">
    <div class="col-12">
        <div class="form-group">
            <label>Tanggal</label>
            <input type="text" class="flatpickr-daterange form-control" placeholder="Pick date range" id="absensi-date" value="{{ Carbon\Carbon::now()->format('Y-m-d') }}" disabled>
        </div>
    
        <div class="form-group">
            <label>Nama Karyawan</label>
            <select type="text" class="select2 form-control @error('user_uuid') is-invalid @enderror"
                name="user_uuid" id = "user_uuid" style="width:100%">
                <option value="">-- Pilih Nama Karyawan --</option>
                @foreach ($users as $user)
                    <option value="{{ $user->uuid }}"> 
                        {{ $user->userInformation->nama }}
                    </option>
                @endforeach
            </select>
            @error('user_uuid')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        @if (!isset($absensi) or $absensi->kategori_absensi->id != 1 ?? null)
        <div class="form-group">
            <label>Kategori</label>
            <select type="text" class="select2 form-control @error('kategori_absensi_uuid') is-invalid @enderror" name="kategori_absensi_uuid">
                {!! each_option($kategori_absensis, 'name', ($absensi->kategori_absensi->uuid ?? old('kategori_absensi_uuid'))) !!}
            </select>
            @error('kategori_absensi_uuid') <span class="text-danger">{{$message}}</span> @enderror
        </div>
        @endif

        <div class="form-group">
            <label>Keterangan</label>
            <textarea class="form-control @error('keterangan') is-invalid @enderror" placeholder="Masukkan keterangan tambahan" name="keterangan">{{ old('keterangan') }}</textarea>
            @error('keterangan') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
    </div>
</div>