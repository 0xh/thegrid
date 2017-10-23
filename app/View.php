<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class View extends Model
{
    protected $fillable = ['job_id', 'user_id', 'ip'];

    public function job() {
        return $this->belongsTo('App\Job');
    }
}
