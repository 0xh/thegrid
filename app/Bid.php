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
    	return $this->belongsTo('App\Job')->with('only_bids', 'user', 'category', 'skills', 'awarded');
    }

    public function jobdetails() {
    	return $this->belongsTo('App\Job');
    }

    public function isApproved() {
    	return $this->hasOne('App\Winner');
    }

    public function winner() {
        return $this->hasOne('App\Winner')->with('review');
    }

    public function scopeInfo($query) {
        return $query->with('job', 'isApproved', 'winner');
    }
}
