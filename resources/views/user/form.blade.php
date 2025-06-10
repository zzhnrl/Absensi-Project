<div class="row p-2">
    <div class="col-12 col-md-4">
        <label for="upload-image">Foto Profil</label>
        <input type="file" id="upload-image" name='image' class="form-control @error('image') is-invalid @enderror" />
        @error('image')
            <span class="text-danger">{{$message}}</span> 
        @enderror
        <img id="preview-image" src="{{ (isset($user) and isset($user->photo)) ? $user->photo->url : asset('img/no_picture.png') }}" alt="your image" width="50%" />
        <small class="form-text text-muted">*Maks 10 Mb dan File wajib PNG atau JPG</small> <!-- ini tambahan teksnya -->.
        @if(isset($user) && isset($user->photo))
            <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" id="remove-picture" name="remove_picture" value="{{ $user->photo->id }}">
                <label class="form-check-label" for="remove-picture">Hapus Foto</label>
            </div>
        @endif
    </div>

    <div class="col-12 col-md-4">
        <label for="upload-image">Tanda Tangan</label>
        <input type="file" id="upload-images" name='images' class="form-control @error('images') is-invalid @enderror" />
        @error('images') 
            <span class="text-danger">{{ $message }}</span> 
        @enderror
        <img id="preview-images" src="{{ (isset($user) and isset($user->signatureFile)) ? $user->signatureFile->url : asset('img/no_picture.png') }}" alt="your image" width="50%" />
        <small class="form-text text-muted">*Maks 10 Mb dan File wajib PNG atau JPG</small> <!-- ini tambahan teksnya -->
        @if(isset($user) && isset($user->signatureFile))
            <div class="form-check mt-2">
                <input class="form-check-input" type="checkbox" id="remove-picture" name="remove_picture" value="{{ $user->signatureFile->id }}"/>
                <label class="form-check-label" for="remove-picture">Hapus Tanda Tangan</label>
            </div>
        @endif
    </div>

    <div class="col-12 col-md-8">
        <div class="form-group">
            <label>Email</label>
            <input type="text" class="form-control @error('email') is-invalid @enderror" placeholder="Masukkan email" name="email" value="{{$user->email ?? old('email')}}">
            @error('email') <span class="text-danger">{{$message}}</span> @enderror
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password" name="password" value="{{$user->password ?? old('password')}}">
            @error('password') <span class="text-danger">{{$message}}</span> @enderror
        </div>

        <div class="form-group">
            <label>Konfirmasi Password</label>
            <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" placeholder="Konfirmasi Password" name="password_confirmation">
            @error('password_confirmation') <span class="text-danger">{{$message}}</span> @enderror
        </div>

        @if (!isset($user) or $user->userRole->role->id != 1 ?? null)
        <div class="form-group">
            <label>Role</label>
            <select type="text" class="select2 form-control @error('role') is-invalid @enderror" name="role">
                {!! each_option($roles, 'name', ($user->userRole->role->uuid ?? old('role'))) !!}
            </select>
            @error('role') <span class="text-danger">{{$message}}</span> @enderror
        </div>
        @endif

        <div class="form-group">
            <label>Nama</label>
            <input type="nama" class="form-control @error('nama') is-invalid @enderror" placeholder="Nama" name="nama" value="{{$user->userInformation->nama ?? old('nama')}}">
            @error('nama') <span class="text-danger">{{$message}}</span> @enderror
        </div>

        <div class="form-group">
            <label>No Telepon</label>
            <input type="notlp" class="form-control @error('notlp') is-invalid @enderror" placeholder="No Telepon" name="notlp" value="{{$user->userInformation->notlp ?? old('notlp')}}">
            @error('notlp') <span class="text-danger">{{$message}}</span> @enderror
        </div>

        <div class="form-group">
            <label>Alamat</label>
            <input type="alamat" class="form-control @error('alamat') is-invalid @enderror" placeholder="Alamat" name="alamat" value="{{$user->userInformation->alamat ?? old('alamat')}}">
            @error('alamat') <span class="text-danger">{{$message}}</span> @enderror
        </div>

        <div class="form-group">
            <label for="sisa_cuti">Kuota Cuti</label>
            <input type="number" class="form-control @error('sisa_cuti') is-invalid @enderror" 
                id="sisa_cuti" 
                name="sisa_cuti" 
                value="{{ old('sisa_cuti', $user->sisa_cuti ?? '') }}">
            @error('sisa_cuti') 
                <span class="text-danger">{{ $message }}</span> 
            @enderror
        </div>



        <input type="hidden" name="user_information" value="{{$user_information->uuid ?? null}}">
    </div>
</div>


<script>
document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("upload-image").addEventListener("change", function (e) {
        const reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById("preview-image").src = e.target.result;
        };
        reader.readAsDataURL(this.files[0]);
    });

    document.getElementById("upload-images").addEventListener("change", function (e) {
        const reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById("preview-images").src = e.target.result;
        };
        reader.readAsDataURL(this.files[0]);
    });
});
</script>
