<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QueryLike extends Model
{
    protected $table = 'query_likes';

    protected $fillable = ['query_id', 'user_id', 'is_liked'];

    public function question() {
        return $this->belongsTo('App\Query');
    }

    public function user() {
        return $this->belongsTo('App\User');
    }
}
