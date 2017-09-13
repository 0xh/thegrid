<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Bid extends Model
{
    use LogsActivity;

    protected $fillable = ['user_id', 'job_id', 'price_bid'];
    protected static $logAttributes = ['user_id', 'job_id', 'price_bid'];

    public function user() {
    	return $this->belongsTo('App\User');
    }

    public function files() {
		return $this->hasMany('App\BidFile');
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
        return $query->with('job', 'isApproved', 'winner', 'files');
    }
}
