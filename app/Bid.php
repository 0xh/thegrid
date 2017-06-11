<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bid extends Model
{
    protected $fillable = ['user_id', 'job_id', 'price_bid'];

    public function user() {
    	return $this->belongsTo('App\User');
    }

    public function job() {
    	return $this->belongsTo('App\Job')->with('user');
    }
}
