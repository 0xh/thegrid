<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Query extends Model
{
    protected $fillable = ['job_id', 'user_id', 'query'];

    public function job() {
        return $this->belongsTo('App\Job');
    }

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function reply() {
        return $this->hasOne('App\Reply');
    }

    public function likes() {
        return $this->hasMany('App\QueryLike')
            ->where('is_liked', true);
    }

    public function dislikes() {
        return $this->hasMany('App\QueryLike')
            ->where('is_liked', false);
    }

    public function scopeInfo($query) {
        return $query->with('user', 'reply', 'likes', 'dislikes');
    }

}
