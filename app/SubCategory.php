<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    public function type() {
        return $this->belongsTo('App\Type');
    }

    public function category() {
        return $this->belongsTo('App\JobCategory');
    }

    public function job() {
        return $this->belongsToMany('App\Job', 'job_tag', 'tag_id', 'job_id');
    }

    public function scopeInfo($query) {
        return $query->with('type', 'category');
    }
}
