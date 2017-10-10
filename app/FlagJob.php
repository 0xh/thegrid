<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FlagJob extends Model
{
    protected $fillable = ['job_id', 'user_id', 'reason', 'status'];

    public function job() {
        return $this->belongsTo('App\Job');
    }

    public function user() {
        return $this->belongsTo('App\User');
    }
}
