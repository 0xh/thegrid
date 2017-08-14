<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
	protected $fillable = ['name', 'user_id', 'price', 'lat', 'lng', 'location', 'category_id', 'date', 'details'];

	public function user() {
		return $this->belongsTo('App\User')->with('profile');
	}

	public function bids() {
		return $this->hasMany('App\Bid')
					->orderBy('price_bid', 'asc');
	}

	public function bidders() {
		return $this->hasMany('App\Bid')
					->with('user')
					->orderBy('price_bid', 'asc');
	}

	public function only_bids() {
		return $this->hasMany('App\Bid')->orderBy('price_bid', 'asc');
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
	

}
