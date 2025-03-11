<div class="row p-2">
    <div class="col-12 col-md-4">
        <label for="upload-image">Foto Bukti</label>
        <input type="file" id="upload-image" name='image' class="form-control @error('image') is-invalid @enderror" />
        @error('image')<span class="text-danger">{{$message}}</span> @enderror
        <img id="preview-image" src="{{ (isset($user) and isset($user->photo)) ? $user->photo->url : asset('img/no_picture.png') }}" alt="your image" width="100%" />
        @if(isset($user) && isset($user->photo))
        <div class="form-check mt-2">
            <input class="form-check-input" type="checkbox" id="remove-picture" name="remove_picture" value="{{ $user->photo->id }}">
            <label class="form-check-label" for="remove-picture">
                Remove Picture
            </label>
        </div>
        @endif
    </div>

    <div class="col-12 col-md-8">
        <div class="form-group">
            <label>Tanggal</label>
            <input type="text" class="flatpickr-daterange form-control" placeholder="Pick date range" name="date_izin_sakit" id="date-izin-sakit" value="{{ Carbon\Carbon::now()->format('Y-m-d') }}" disabled>
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

        <input type="hidden" name="user_information" value="{{$user_information->uuid ?? null}}">
    </div>
</div>