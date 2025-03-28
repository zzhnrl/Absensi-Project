<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointUser extends Model
{
    use HasFactory;

    protected $table = 'point_users';
    protected $guarded = ['id'];

    public $timestamps = false;

protected $fillable = [
    'uuid', // Tambahkan UUID di sini
    'bulan',
    'tahun',
    'user_id',
    'jumlah_point',
    'nama_karyawan'
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
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'user_id')->where('deleted_at', null); 
    }

    public function rekapIzinSakit()
    {
        return $this->hasOne(RekapIzinSakit::class, 'user_id', 'id'); 
    }
}
