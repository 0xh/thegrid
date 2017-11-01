<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use DB;

class Job extends Model
{
	use LogsActivity, SoftDeletes;

	protected $fillable = ['name', 'user_id', 'price', 'lat', 'lng', 'location', 'category_id', 'date', 'status', 'details'];
	protected static $logAttributes = ['name', 'user_id', 'price', 'lat', 'lng', 'location', 'category_id', 'date', 'status', 'details'];
	protected $dates = ['deleted_at'];

	public function user() {
		return $this->belongsTo('App\User')->with('profile', 'rating', 'country');
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

	public function flags() {
		return $this->hasMany('App\FlagJob');
	}

	public function views() {
		return $this->hasMany('App\View');
	}

	public function viewsThisWeek() {
		return $this->hasMany('App\View')//->select([DB::raw('count(job_id) as count'), DB::raw('Date(created_at) as day')])->groupBy('day');
			->select([
				'job_id',
				// This aggregates the data and makes available a 'count' attribute
				DB::raw('count(id) as `count`'), 
				// This throws away the timestamp portion of the date
				DB::raw('DATE(created_at) as day')
			// Group these records according to that day
			])->groupBy('day')
			// And restrict these results to only those created in the last week
			->where('created_at', '>=', Carbon::now()->subWeeks(1));
	}

	public function viewsThisMonth() {
		return $this->hasMany('App\View')
			->select(['job_id', DB::raw('count(id) as `count`'),DB::raw('DATE(created_at) as day')])
			->groupBy('day')
			->where('created_at', '>=', Carbon::now()->subMonths(1));
	}

	public function offersThisWeek() {
		return $this->hasMany('App\Bid')
			->select(['job_id', DB::raw('count(id) as `count`'),DB::raw('DATE(updated_at) as day')])
			->groupBy('day')
			->where('updated_at', '>=', Carbon::now()->subWeeks(1));
	}
	
	public function offersThisMonth() {
		return $this->hasMany('App\Bid')
			->select(['job_id', DB::raw('count(id) as `count`'),DB::raw('DATE(updated_at) as day')])
			->groupBy('day')
			->where('updated_at', '>=', Carbon::now()->subMonths(1));
	}

	public function scopeSearch($query, $s) {
		return $query->where('name', 'like', '%' . $s . '%')
								 ->orWhere('location', 'like', '%' . $s . '%')
								 ->with('user', 'bids', 'files');
	}

	public function skills() {
		return $this->belongsToMany('App\Skill');
	}

	public function category() {
		return $this->hasOne('App\JobCategory', 'id', 'category_id');
	}

	public function scopeInfo($query) {
			return $query->with('user', 'category', 'skills', 'files', 'flags', 'views');
	}

	public function scopeInfoWithBidders($query) {
		return $query->with('user', 'category', 'skills', 'files', 'bidders', 'winner', 'conversation', 'flags', 'views', 'viewsThisWeek', 'viewsThisMonth', 'offersThisWeek', 'offersThisMonth');
	}

}
