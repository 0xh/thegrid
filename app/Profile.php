<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Profile extends Model
{
  use LogsActivity;

  protected $fillable = [
      'user_id', 'first_name', 'middle_name', 'last_name',
      'birth_date', 'address', 'profile_image_url', 'bio'
  ];
  
  protected static $logAttributes = [
      'user_id', 'first_name', 'middle_name', 'last_name',
      'birth_date', 'address', 'profile_image_url', 'bio'
  ];

  public function user() {
    return $this->belongsTo('App\User');
  }

  public function getBirthDateAttribute($value) {
    return date("m/d/Y", strtotime($value));
  }
}
