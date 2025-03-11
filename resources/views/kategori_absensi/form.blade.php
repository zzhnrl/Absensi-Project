<div class="row">
    <div class="col-12">
        <div class="form-group">
            <label>Nama Kategori Absen</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" placeholder="Masukkan nama kategori absen" name="name" value="{{$kategori_absensi->name ?? old('name')}}">
            @error('name') <span class="text-danger">{{$message}}</span> @enderror
        </div>

        <div class="form-group">
            <label>Kode Kategori Absen</label>
            <input type="text" class="form-control @error('code') is-invalid @enderror" placeholder="Masukkan kode kategori absensi" name="code" value="{{$kategori_absensi->code ?? old('code')}}">
            @error('code') <span class="text-danger">{{$message}}</span> @enderror
        </div>

        <div class="form-group">
            <label>Point Kategori Absen</label>
            <input type="text" class="form-control @error('point') is-invalid @enderror" placeholder="Masukkan point kategori absensi" name="point" value="{{$kategori_absensi->point ?? old('point')}}">
            @error('point') <span class="text-danger">{{$message}}</span> @enderror
        </div>

        <div class="form-group">
            <label>Deskripsi Kategori Absen</label>
            <textarea type="text" class="form-control @error('description') is-invalid @enderror" placeholder="Masukan deskripsi kategori absensi" name="description">{{$kategori_absensi->description ?? old('description')}}</textarea>
            @error('description') <span class="text-danger">{{$message}}</span> @enderror
        </div>
    </div>
</div>