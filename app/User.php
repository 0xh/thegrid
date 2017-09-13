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
        'password', 'gender', 'birth_date',
        'confirmation_token', 'confirmation_code',
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

    public function settings() {
        return $this->hasMany('App\Setting');
    }

    public function skills() {
        return $this->belongsToMany('App\Skill');
    }

    public function reviews() {
        return $this->hasMany('App\Review');
    }

    public function locations() {
        return $this->hasMany('App\Location');
    }

    public function scopeInfo($query) {
        return $query->with('profile', 'reviews', 'locations')->withCount('jobs')->withCount('bids');
    }
    

}
