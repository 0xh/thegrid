<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'feedbacks';
    protected $fillable = ['user_id', 'title', 'type', 'tab', 'error_type',
                           'occurence', 'description', 'name', 'email', 'feedback'];

}