<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Job;
use App\Bid;
use App\User;
use App\Winner;

class BidController extends Controller
{
    public function bid(Request $request, $id) {
			$data = $request->all();
			$bidded = Bid::where([
				['user_id', '=', $id],
				['job_id', '=', $data['job_id']]
			])->first();
    	if( !$bidded ) {
	    	$bid = Bid::create([
	    		'user_id' => $id,
            'job_id' => $data['job_id'],
            'price_bid' => $data['price_bid']
        ]);
	    	$user = User::where('id', $id)->first();
	    	$job = Job::info()->where('id',$data['job_id'])->first();
	    	$bid->job = $job;
	    	$bid->user = $user;
	      return response()->json($bid, 200);
	    } else {
	    	return response()->json(['error' => true, 'message' => 'Already bidded'], 422);
	    }
    }

    public function approveBid(Request $request) {
    	$data = $request->all();
    	$winner = Winner::create([
    			'bid_id' => $data['id'],
    			'user_id' => $data['user_id'],
    			'job_id' => $data['job_id']
    		]);
    	if($winner) {
    		$job = Job::find($data['job_id'])->first();
    		$user = $data['user'];
    		$winner->job = $job;
    		$winner->user = $user;
    		$bid = Bid::where([
	    				['id', '=', $data['id']],
	    				['user_id', '=', $data['user_id']]
    				])
    				->with('user')
    				->with('job')
    				->with('isApproved')
    				->first();
    		return response()->json($bid, 200);
    	}
    	return response()->json(['error' => true, 'message' => 'An error occured'], 500);
    }

    public function getBids($id) {
    	$bids = Bid::where('user_id', $id)
    				->with('user')
    				->with('job')
    				->with('isApproved')
    				->orderBy('created_at', 'desc')
    				->paginate(env('BIDS_PER_PAGE'));
    	return response()->json($bids, 200);
    }

    public function getBidDetails($id, $bid_id) {
    	$bid = Bid::where([
    			['id', '=', $bid_id],
    			['user_id', '=', $id]
    		])->with('job')
    		->with('isApproved')
    		->first();

    	return $bid;
    }

    protected function isBidded($id, $job_id) {
    	$bid = Bid::where([
    					['user_id', '=', $id],
    					['job_id', '=', $job_id]
    				])->first();
    	if($bid) {
    		return response()->json(['bidded' => true]);
    	}
    	return response()->json(['bidded' => false]);
    }
}
