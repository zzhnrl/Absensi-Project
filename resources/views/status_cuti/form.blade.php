<div class="row">
    <div class="col-12">
        <div class="form-group">
            <label>Nama Status</label>
            <input type="text" class="form-control @error('nama') is-invalid @enderror" placeholder="Masukkan nama status cuti" name="nama" value="{{$status_cuti->nama ?? old('nama')}}">
            @error('nama') <span class="text-danger">{{$message}}</span> @enderror
        </div>

        <div class="form-group">
            <label>Kode Status Cuti</label>
            <input type="text" class="form-control @error('kode') is-invalid @enderror" placeholder="Masukkan kode status cuti" name="kode" value="{{$status_cuti->kode ?? old('kode')}}">
            @error('kode') <span class="text-danger">{{$message}}</span> @enderror
        </div>

        <div class="form-group">
            <label>Deskripsi Status Cuti</label>
            <textarea type="text" class="form-control @error('deskripsi') is-invalid @enderror" placeholder="Masukan deskripsi status cuti" name="deskripsi">{{$status_cuti->deskripsi ?? old('deskripsi')}}</textarea>
            @error('deskripsi') <span class="text-danger">{{$message}}</span> @enderror
        </div>
    </div>
</div>