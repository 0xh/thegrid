<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JobFile extends Model
{
    protected $fillable = ['job_id', 'name', 'path', 'type', 'file_size', 'status'];

    public function job() {
        return $this->belongsTo('App\Job');
    }
}
