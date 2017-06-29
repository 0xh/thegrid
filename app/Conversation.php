<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    public function job() {
    	return $this->belongsTo('App\Job');
    }

    public function user1() {
    	return $this->belongsTo('App\User', 'user_id_1');
    }

    public function user2() {
    	return $this->belongsTo('App\User', 'user_id_2');
    }

}
