<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriAbsensi extends Model
{
    use HasFactory;

    protected $table = 'kategori_absensis';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'code',
        'point',
        'description',
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
}
