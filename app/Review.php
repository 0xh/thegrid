<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['user_id', 'job_id', 'review','stars', 'star_up', 'star_down'];

    public function job() {
        return $this->belongsTo('App\Job');
    }
}
