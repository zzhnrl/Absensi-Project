<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    use HasFactory;
    protected $table = 'notifikasi';

    protected $fillable = [
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

    protected $appends = [
        'format_time'
    ];

    public $timestamps = false;

    public function getFormatTimeAttribute () {
        return isset($this->notification_time) ? Carbon::parse($this->notification_time)->diffForHumans() : '-';
    }

    public function user () {
        return $this->belongsTo(User::class, 'mstr_user_id');
    }

}
