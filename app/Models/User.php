<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';
    protected $guarded = ['id'];

    public $timestamps = false;

    protected $fillable = [
        'email',
    ];

    protected $hidden = [
        'id',
        'photo_id',
        'signature_file_id',
        'password',
        'remember_token',
        'created_by',
        'updated_by',
        'deleted_by',
        'updated_at',
        'deleted_at',
        'is_active',
        'version'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function userRole()
    {
        return $this->hasOne(UserRole::class, 'user_id');
    }

    public function userInformation()
    {
        return $this->hasOne(UserInformation::class, 'user_id');
    }

    public function photo()
    {
        return $this->belongsTo(FileStorage::class, 'photo_id');
    }

    public function signatureFile()
    {
        return $this->belongsTo(FileStorage::class, 'signature_file_id');
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'user_id', 'id');
    } 

    public function rekapIzinSakit()
    {
        return $this->hasOne(RekapIzinSakit::class, 'user_id', 'id');
    }


}
