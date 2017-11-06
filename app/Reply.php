<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    protected $fillable = ['query_id', 'reply'];

    public function question() {
        return $this->belongsTo('App\Query');
    }
}
