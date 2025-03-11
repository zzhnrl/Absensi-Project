<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class FileStorage extends Model
{
    use HasFactory;

    protected $table = 'file_storages';
    public $timestamps = false;

    protected $fillable = [
        'is_used'
    ];

    protected $hidden = [
        'id',
        'photo_id',
        'size',
        'extension',
        'name',
        'location',
        'created_by',
        'updated_by',
        'deleted_by',
        'updated_at',
        'deleted_at',
        'is_active',
        'is_used',
        'filesystem',
        'segment',
        'remark',
        'version'
    ];

    protected $appends = [
        'url',
    ];

    public function getUrlAttribute()
    {
        if ($this->filesystem == 'public') {
            return url("/storage/" . $this->location ."/".$this->name.".".$this->extension);
        } else if ($this->filesystem == 's3') {
            return Storage::disk('s3')->temporaryUrl("$this->location/$this->name.$this->extension",now()->addMinutes(5));
        }
    }


    public function generateUrl ($type = 'public') {
        if ($type == 'public') {
            $this->url = url("/storage/" . $this->location ."/".$this->name.".".$this->extension);
        } else if ($type == 's3') {
            $this->url = Storage::disk('s3')->temporaryUrl("$this->location/$this->name.$this->extension",now()->addMinutes(5));
        }
        return $this;

    }

    public function user () {
        return $this->hasOne(User::class, 'photo_id');
    }
}
