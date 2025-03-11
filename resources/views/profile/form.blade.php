<div class="row p-2">
    <div class="col-12 col-md-4 float-right">
        <label for="upload-image">Foto Profil</label>
        <input type="file" id="upload-image" name='image' class="form-control @error('image') is-invalid @enderror"/>
        @error('image')<span class="text-danger">{{$message}}</span> @enderror
        <img id="preview-image" src="{{ (isset($user) and isset($user->photo)) ? $user->photo->generateUrl()->url : asset('img/no_picture.png') }}" alt="your image" width="100%" />
    </div>
    <div class="col-12 col-md-8">

        <div class="form-group">
            <label>Username</label>
            <input type="text" class="form-control @error('username') is-invalid @enderror" placeholder="Username" name="username" value="{{$user->username ?? old('username')}}">
            @error('username') <span class="text-danger">{{$message}}</span> @enderror
        </div>

        <div class="form-group">
            <label>Nama</label>
            <input type="text" class="form-control @error('nama') is-invalid @enderror" placeholder="Nama lengkap" name="nama" value="{{$user->pegawai->nama ?? old('nama')}}">
            @error('nama') <span class="text-danger">{{$message}}</span> @enderror
        </div>

        <div class="form-group">
            <label>Alamat Email</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" placeholder="Masukkan Email" name="email" value="{{$user->pegawai->email ?? old('email')}}">
        @error('email') <span class="text-danger">{{$message}}</span> @enderror
        </div>

        <div class="form-group">
            <label>No Handphone</label>
            <input type="text" class="form-control @error('nomor_telepon') is-invalid @enderror" placeholder="Nomor Handphone" name="nomor_telepon" value="{{$user->pegawai->nomor_telepon ?? old('nomor_telepon')}}">
            @error('nomor_telepon') <span class="text-danger">{{$message}}</span> @enderror
        </div>

        <div class="form-group">
            <label>Old Password</label>
            <input type="password" class="form-control @error('old_password') is-invalid @enderror" placeholder="Old Password" name="old_password">
            @error('old_password') <span class="text-danger">{{$message}}</span> @enderror
        </div>

        <div class="form-group">
            <label>New Password</label>
            <input type="password" class="form-control @error('new_password') is-invalid @enderror" placeholder="New Password" name="new_password">
            @error('new_password') <span class="text-danger">{{$message}}</span> @enderror
        </div>

        <div class="form-group">
            <label>Retype New Password</label>
            <input type="password" class="form-control @error('new_password_confirmation') is-invalid @enderror" placeholder="Retype New Password" name="new_password_confirmation" >
            @error('new_password_confirmation') <span class="text-danger">{{$message}}</span> @enderror
        </div>

    </div>
</div>

