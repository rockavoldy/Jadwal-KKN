<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    //
    protected $fillable = ['tanggal', 'mulai', 'akhir', 'deskripsi', 'img'];

    public function user_activity()
    {
        $this->hasMany(UserActivity::class);
    }
}
