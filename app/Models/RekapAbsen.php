<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekapAbsen extends Model
{
    use HasFactory;

    protected $table = 'rekap_absen';
    protected $guarded = ['id'];

    public $timestamps = false;

    protected $fillable = [
        'nama_karyawan',
        'bulan',
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
        return $this->hasOne(User::class, 'user_id');
    }

    public function userInformation()
    {
        return $this->hasOne(UserInformation::class, 'user_id');
    }
}
