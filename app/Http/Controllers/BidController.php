<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Job;
use App\Bid;
use App\User;

class BidController extends Controller
{
    public function bid(Request $request) {
    	$data = $request->all();
    	$bid = Bid::create([
    		'user_id' => $data['user_id'],
            'job_id' => $data['job_id'],
            'price_bid' => $data['price_bid']
        ]);
    	$user = User::find($data['user_id']);
    	$job = Job::find($data['job_id']);
    	$bid->job = $job;
    	$bid->user = $user;
        return $bid;
    }

    public function getBids($id) {
    	return Bid::where('user_id', $id)
    				->with('user')
    				->with('job')
    				->orderBy('created_at', 'desc')
    				->get();
    }

    public function isBidded($id, $job_id) {
    	$bid = Bid::where([
    					['user_id', '=', $id],
    					['job_id', '=', $job_id]
    				])->first();
    	if($bid) {
    		return ['isBidded' => true];
    	}
    	return ['isBidded' => false];
    }
}
