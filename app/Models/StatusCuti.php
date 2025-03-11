<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusCuti extends Model
{
    use HasFactory;

    protected $table = 'status_cutis';

    public $timestamps = false;

    protected $fillable = [
        'nama',
        'kode',
        'deskripsi'
    ];

    protected $hidden = [
        'id',
        'created_by',
        'updated_by',
        'deleted_by',
        'updated_at',
        'deleted_at',
        'is_active',
        'version'
    ];

    public static function listActiveStatus(array $status_cuti_id = [1])
    {
        return StatusCuti::whereNotIn('id', $status_cuti_id)->where('is_active', 1)->where('deleted_at', null)->orderBy('name', 'asc')->get();
    }
}
