<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cuti extends Model
{
    use HasFactory;

    protected $table = 'cutis';
    protected $guarded = ['id'];

    public $timestamps = false;

    protected $fillable = [
        'tanggal',
        'perihal',
        'keterangan',
        'approve_at',
        'approve_by',
        'reject_at',
        'reject_by',
        'jenis_cuti',
    ];

    protected $hidden = [
        'id',
        'user_id',
        'status_cuti_id',
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
    
    public function statusCuti()
    {
        return $this->belongsTo(StatusCuti::class, 'status_cuti_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function approveByUser()
    {
        return $this->belongsTo(User::class, 'approve_by', 'id');
    }

    public function rejectByUser()
    {
        return $this->belongsTo(User::class, 'reject_by', 'id');
    }

    public function userInformation()
    {
        return $this->hasOne(UserInformation::class, 'user_id', 'id');
    }

}
