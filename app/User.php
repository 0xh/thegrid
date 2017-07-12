<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'phone_number', 'password', 'confirmation_token', 'confirmation_code',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'confirmation_token', 'confirmation_code',
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

}
