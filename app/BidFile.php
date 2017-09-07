<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BidFile extends Model
{
    protected $fillable = ['bid_id', 'name', 'original_name', 'path', 'type', 'file_size', 'status'];
}
