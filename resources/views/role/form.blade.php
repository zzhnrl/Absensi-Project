<div class="row">
    <div class="col-12">
        <div class="form-group">
            <label>Nama Role</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" placeholder="Masukkan nama role" name="name" value="{{$role->name ?? old('name')}}">
            @error('name') <span class="text-danger">{{$message}}</span> @enderror
        </div>

        <div class="form-group">
            <label>Kode Role</label>
            <input type="text" class="form-control @error('code') is-invalid @enderror" placeholder="Masukkan kode role" name="code" value="{{$role->code ?? old('code')}}">
            @error('code') <span class="text-danger">{{$message}}</span> @enderror
        </div>

        <div class="form-group">
            <label>Deskripsi Role</label>
            <textarea type="text" class="form-control @error('description') is-invalid @enderror" placeholder="Masukan deskripsi role" name="description">{{$role->description ?? old('description')}}</textarea>
            @error('description') <span class="text-danger">{{$message}}</span> @enderror
        </div>
    </div>
</div>