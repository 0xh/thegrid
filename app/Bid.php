<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bid extends Model
{
    use LogsActivity, SoftDeletes;

    protected $fillable = ['user_id', 'job_id', 'price_bid'];
    protected static $logAttributes = ['user_id', 'job_id', 'price_bid'];

    public function user() {
        return $this->belongsTo('App\User')
            ->with('profile', 'reviews', 'rating', 'country');
    }

    public function files() {
		return $this->hasMany('App\BidFile');
	}

    public function job() {
        return $this->belongsTo('App\Job')
            ->with('bids', 'user', 'category', 'tags', 'awarded', 'conversation', 'files', 'questions', 'country')
            ->withCount('views', 'bidders', 'questions');;
    }

    public function jobdetails() {
    	return $this->belongsTo('App\Job');
    }

    public function isApproved() {
    	return $this->hasOne('App\Winner');
    }

    public function winner() {
        return $this->hasOne('App\Winner')
            ->with('review');
    }

    public function scopeInfo($query) {
        return $query->with('job', 'isApproved', 'winner', 'files');
    }
}
