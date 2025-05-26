<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoryPointUser extends Model
{
    // Non-aktifkan timestamps jika tak diperlukan
    public $timestamps = true;

    protected $table = 'history_point_users';

    protected $fillable = [
        'user_id',
        'jumlah_point',
        'perubahan_point',
        'tanggal',
    ];

    protected $casts = [
        'tanggal'    => 'date',        // agar $item->tanggal jadi Carbon instance
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
