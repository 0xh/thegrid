<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
	protected $fillable = ['name', 'user_id', 'price', 'lat', 'lng', 'location', 'category_id', 'date'];

	public function user() {
    	return $this->belongsTo('App\User');
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
}
