<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInformation extends Model
{
    use HasFactory;

    protected $table = 'user_informations';
    protected $guarded = ['id'];

    public $timestamps = false;

    protected $fillable = [
        'nama',
        'notlp',
        'alamat',
    ];

    protected $hidden = [
        'id',
        'signature_file_id',
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

    public function signatureFile()
    {
        return $this->belongsTo(FileStorage::class, 'signature_file_id');
    }
}
