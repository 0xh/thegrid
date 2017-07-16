<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'user_id', 'first_name', 'middle_name', 'last_name', 'phone_number',
        'birth_date', 'address', 'profile_image_url'
    ];

    public function user() {
      return $this->belongsTo('App\User');
    }

    public function getBirthDateAttribute($value) {
     return date("m/d/Y", strtotime($value));
   }
}
