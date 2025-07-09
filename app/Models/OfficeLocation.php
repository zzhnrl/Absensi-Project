<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficeLocation extends Model
{
    use HasFactory;

    protected $table = 'office_locations'; // Nama tabel di database

    protected $fillable = [
        'address', // Tambahkan kolom ini
        'latitude',
        'longitude',
        'index'

    ];
}
