<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['supplier_id', 'customer_id', 'amount', 'job_id', 'bid_id', 'status', 'payment_type'];


    public function supplier() {
        return $this->belongsTo('App\User', 'supplier_id');
    }

    public function customer() {
        return $this->belongsTo('App\User', 'customer_id');
    }

    public function job() {
        return $this->belongsTo('App\Job')->with('category', 'country');
    }

    public function bid() {
        return $this->belongsTo('App\Bid');
    }

    public function scopeInfo($query) {
        return $query->with('supplier', 'customer', 'job', 'bid');
    }
}
