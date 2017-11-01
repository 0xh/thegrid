<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Winner extends Model
{
    protected $fillable = [
        'bid_id', 'user_id', 'job_id'
    ];

    public function user() {
        return $this->belongsTo('App\User')
            ->with('country');
    }

    public function job() {
    	return $this->belongsTo('App\Job');
    }

    public function bid() {
    	return $this->belongsTo('App\Bid');
    }

    public function review() {
        return $this->hasOne('App\Review', 'job_id', 'job_id');
    }

}
