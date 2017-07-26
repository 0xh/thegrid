<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    // protected $hidden = ['pivot'];

    public function users() {
    	return $this->belongsToMany('App\User');
    }


    public function usersCount() {
      return $this->belongsToMany('App\User')
                  ->selectRaw('count(users.id) as aggregate')
                  ->groupBy('pivot_skill_id');
    }

    public function getUsersCountAttribute() {
        if ( ! array_key_exists('usersCount', $this->relations)) $this->load('usersCount');

        $related = $this->getRelation('usersCount')->first();

        return ($related) ? $related->aggregate : 0;
    }
}
