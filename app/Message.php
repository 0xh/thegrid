<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
	protected $fillable = [
        'conversation_id', 'author_id', 'recipient_id', 'message'
    ];

    public function author() {
    	return $this->belongsTo('App\User', 'author_id');
    }

    public function recipient() {
    	return $this->belongsTo('App\User', 'recipient_id');
    }
}
