<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensis';
    protected $guarded = ['id'];

    public $timestamps = false;

    protected $fillable = [
        'kategori_absensi_id',
        'keterangan',
        'jam_masuk',
        'status_absen',
        'uuid',
        'user_id',
        'nama_karyawan',
        'nama_kategori',
        'tanggal',
        'jumlah_point',
        'bukti_foto_dikantor',
    ];

    protected $hidden = [
        'id',
        'user_id',
        'created_by',
        'updated_by',
        'deleted_by',
        'updated_at',
        'deleted_at',
        'is_active',
        'version'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'user_id', 'id');
    }

    public function userInformation()
    {
        return $this->hasOne(UserInformation::class, 'user_id');
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriAbsensi::class, 'kategori_absensi_id');
    }

    // public function pointUser()
    // {
    //     return $this->belongsTo(PointUser::class, 'user_id'); 
    // }
}
