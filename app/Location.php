<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Location extends Model
{
    use LogsActivity;

    protected $fillable = ['user_id', 'location', 'lat', 'lng', 'alias'];
    protected static $logAttributes = ['user_id', 'location', 'lat', 'lng', 'alias'];
}
