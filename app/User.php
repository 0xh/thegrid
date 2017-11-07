<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'username', 'email', 'phone_number',
        'password', 'gender', 'birth_date',
        'confirmation_token', 'confirmation_code',
    ];
    
    protected static $logAttributes = [
        'name', 'username', 'email', 'phone_number',
        'gender', 'birth_date'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'confirmation_token', 'confirmation_code', 'pivot'
    ];

    public function items() {
        return $this->hasMany('App\Item');
    }

    public function socialProviders() {
        return $this->hasMany('App\SocialProvider');
    }

    public function jobs() {
        return $this->hasMany('App\Job');
    }

    public function bids() {
        return $this->hasMany('App\Bid');
    }

    public function profile() {
        return $this->hasOne('App\Profile');
    }

    public function conversations() {
        return $this->hasMany('App\Conversation');
    }

    public function messages() {
        return $this->hasMany('App\Message');
    }

    public function unreadMessages() {
        return $this->hasMany('App\Message', 'recipient_id')
            ->where('status', 0);
    }

    public function settings() {
        return $this->hasMany('App\Setting');
    }

    public function skills() {
        return $this->belongsToMany('App\Skill');
    }

    public function reviews() {
        return $this->hasMany('App\Review');
    }

    public function rating() {
        return $this->reviews()
            ->selectRaw('avg(stars) as star, user_id')
            ->groupBy('user_id');
    }

    public function locations() {
        return $this->hasMany('App\Location');
    }

    public function country() {
        return $this->belongsTo('App\Country');
    }

    public function completedJobs() {
        return $this->hasMany('App\Winner')
                    ->with('job')
                    ->whereHas('job', function($query) {
                        $query->where('is_completed', true);
                    });
        // return $this->hasMany('App\Job')
        //             ->whereHas('winner', function($query) {

        //             });
    }

    public function scopeInfo($query) {
        return $query->with('profile', 'reviews', 'locations', 'country')
                     ->withCount('jobs')
                     ->withCount('bids')
                     ->withCount('unreadMessages');
    }

    public function getRatingAttribute()
    {
        if ( ! array_key_exists('rating', $this->relations)) {
           $this->load('rating');
        }
    
        $relation = $this->getRelation('rating')->first();
    
        return ($relation) ? $relation->star : null;
    }
    

}
