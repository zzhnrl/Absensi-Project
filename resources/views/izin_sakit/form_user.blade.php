<div class="row p-2">
    <div class="col-12 col-md-4">
        <label for="upload-image">Foto Bukti</label>
        <input type="file" id="upload-image" name='imagess' class="form-control @error('image') is-invalid @enderror" required/>
        @error('image')<span class="text-danger">{{$message}}</span> @enderror
        <img id="preview-image" src="{{ (isset($user) and isset($user->photo)) ? $user->photo->url : asset('img/no_picture.png') }}" alt="your image" width="100%" />
        <small class="form-text text-muted">*Maks 10 Mb dan File wajib PNG atau JPG</small> <!-- ini tambahan teksnya -->
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
            <input type="text" class="flatpickr-daterange form-control" placeholder="Pick date range" name="date_izin_sakit" id="date-izin-sakit" value="{{ \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d') }}" disabled>
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

        <input type="hidden" name="user_information" value="{{$user_information->uuid ?? null}}">
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.getElementById('upload-image').addEventListener('change', function(event) {
    const file = event.target.files[0];

    if (file) {
        const maxSizeInMB = 10;
        const maxSizeInBytes = maxSizeInMB * 1024 * 1024;

        if (file.size > maxSizeInBytes) {
            Swal.fire({
                icon: 'error',
                title: 'Ukuran File Terlalu Besar',
                text: 'Maksimum ukuran file adalah 10MB.',
                confirmButtonColor: '#d33'
            });
            event.target.value = ""; // Reset input file
            document.getElementById('preview-image').src = "{{ asset('img/no_picture.png') }}";
        } else {
            // Tampilkan preview gambar jika valid
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview-image').src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    }
});
</script>
