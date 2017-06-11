<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
	protected $fillable = ['name', 'user_id', 'price', 'lat', 'lng', 'location', 'category_id'];

	public function user() {
    	return $this->belongsTo('App\User');
    }

    public function bids() {
    	return $this->hasMany('App\Bid')
    				->orderBy('price_bid', 'asc')
    				->with('user');
    }
}
