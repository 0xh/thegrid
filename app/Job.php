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

	protected $fillable = ['name', 'user_id', 'price', 'country_id', 'lat', 'lng', 'location', 'category_id', 'date', 'status', 'details'];
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

	public function country() {
		return $this->belongsTo('App\Country');
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
		return $this->hasMany('App\View')
			->select(['job_id', 'created_at', DB::raw('count(id) as `count`'), DB::raw('DATE(created_at) as day')])
			->groupBy('day')
			->where('created_at', '>=', Carbon::now()->subWeeks(1));
		// return $this->hasMany('App\View')
		// 	->where('created_at', '>=', \Carbon\Carbon::now()->subWeeks(1));
			// ->groupBy(function($item){ return $item->created_at->format('Y-m-d H:i:s'); });
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

	public function tags() {
		return $this->belongsToMany('App\SubCategory', 'job_tag', 'job_id', 'tag_id');
	}

	public function category() {
		return $this->hasOne('App\JobCategory', 'id', 'category_id');
	}

	public function questions() {
		return $this->hasMany('App\Query')
			->with('user', 'reply', 'likes', 'dislikes')
			->orderBy('created_at', 'desc');
	}

	public function scopeInfo($query) {
		return $query->with('user', 'category', 'tags', 'files', 'flags', 'questions', 'only_bids', 'country')
			->withCount('views', 'bidders', 'questions');
	}

	public function scopeInfoWithBidders($query) {
		return $query->with('user', 'category', 'tags', 'files', 'bidders', 'winner', 'conversation', 'flags', 'viewsThisWeek', 'viewsThisMonth', 'offersThisWeek', 'offersThisMonth', 'questions', 'country')
			->withCount('views', 'bidders', 'questions');
	}
	public function scopeTest($query) {
		return $query->with('viewsThisWeek', 'viewsThisMonth', 'offersThisWeek', 'offersThisMonth', 'questions');			
	}

}
