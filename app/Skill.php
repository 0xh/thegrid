<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
  // protected $hidden = ['pivot'];

  protected $fillable = ['skill'];

  public function users() {
    return $this->belongsToMany('App\User');
  }

  public function jobs() {
    return $this->belongsToMany('App\Job');
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
