<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Job extends Model
{
	use LogsActivity;

	protected $fillable = ['name', 'user_id', 'price', 'lat', 'lng', 'location', 'category_id', 'date', 'status', 'details'];
	protected static $logAttributes = ['name', 'user_id', 'price', 'lat', 'lng', 'location', 'category_id', 'date', 'status', 'details'];

	public function user() {
		return $this->belongsTo('App\User')->with('profile');
	}

	public function bids() {
		return $this->hasMany('App\Bid')
					->orderBy('price_bid', 'asc');
	}

	public function bidders() {
		return $this->hasMany('App\Bid')
					// ->with('winner')
					->with('user', 'files')
					->orderBy('price_bid', 'asc');
	}

	public function winner() {
		return $this->hasOne('App\Winner')->with('user', 'bid', 'review');
	}

	public function files() {
		return $this->hasMany('App\JobFile')->where('status', 0);
	}
	
	public function awarded() {
		return $this->hasOne('App\Winner');
	}

	public function only_bids() {
		return $this->hasMany('App\Bid')->orderBy('price_bid', 'asc');
	}

	public function conversation() {
		return $this->hasOne('App\Conversation');
	}

	public function scopeSearch($query, $s) {
		return $query->where('name', 'like', '%' . $s . '%')->orWhere('location', 'like', '%' . $s . '%');
	}

	public function skills() {
		return $this->belongsToMany('App\Skill');
	}

	public function category() {
		return $this->hasOne('App\JobCategory', 'id', 'category_id');
	}

	public function scopeInfo($query) {
			return $query->with('user', 'category', 'skills', 'files');
	}

	public function scopeInfoWithBidders($query) {
		return $query->with('user', 'category', 'skills', 'files', 'bidders', 'winner', 'conversation');
	}
	

}
