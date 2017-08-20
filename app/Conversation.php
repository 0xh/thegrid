<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = ['job_id', 'user_id_1', 'user_id_2', 'last_updated_by', 'unread_count', 'is_deleted'];

    public function job() {
    	return $this->belongsTo('App\Job');
    }

    public function user1() {
    	return $this->belongsTo('App\User', 'user_id_1')->with('profile');
    }

    public function user2() {
    	return $this->belongsTo('App\User', 'user_id_2')->with('profile');
    }

    public function messages() {
        return $this->hasMany('App\Message');
    }

    public function scopeInfo($query) {
        return $query->with('job', 'user1', 'user2');
    }

    public function scopeInfoWithMessages($query) {
        return $query->with('job', 'user1', 'user2', 'messages');
    }

}
