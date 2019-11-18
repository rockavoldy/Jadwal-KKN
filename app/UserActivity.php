<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    //
    protected $fillable = ['activity_id', 'user_id'];

    public function user()
    {
        $this->belongsTo(User::class);
    }

    public function activity()
    {
        $this->belongsTo(Activity::class);
    }
}
