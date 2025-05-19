<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IzinSakit extends Model
{
    use HasFactory;

    protected $table = 'izin_sakits';
    protected $guarded = ['id'];

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'keterangan',
    ];

    protected $hidden = [
        'id',
        'photo_id',
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

    public function photo()
    {
        return $this->belongsTo(FileStorage::class, 'photo_id');
    }
}
