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

        <div class="form-group">
            <label>Keterangan</label>
            <textarea class="form-control @error('keterangan') is-invalid @enderror" placeholder="Masukkan keterangan tambahan" name="keterangan">{{ old('keterangan') }}</textarea>
            @error('keterangan') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
    </div>
</div>