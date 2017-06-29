<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Winner extends Model
{
    protected $fillable = [
        'bid_id', 'user_id', 'job_id'
    ];

    public function user() {
    	return $this->hasOne('App\User');
    }

    public function job() {
    	return $this->hasOne('App\Job');
    }

    public function bid() {
    	return $this->hasOne('App\Bid');
    }

}
