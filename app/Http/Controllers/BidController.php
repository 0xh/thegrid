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
			// $user = User::where('id', $id)->first();
			// $job = Job::info()->where('id', $data['job_id'])->first();
			// $bid->job = $job;
			// $bid->user = $user;
			$bid = Bid::info()->where('id', $bid->id)->first();

			return response()->json($bid, 200);
		} else {
			return response()->json(['error' => true, 'message' => 'Already bidded'], 422);
		}
	}

	public function approveBid(Request $request, $id, $bid_id) {
		$data = $request->all();
		$winner = Winner::create([
				'bid_id' => $bid_id,
				'user_id' => $data['user_id'],
				'job_id' => $data['job_id']
			]);
		if($winner) {
			$job = Job::infoWithBidders()->where('id', $data['job_id'])->first();
			$job->status = 1;
			$job->save();

			// $user = User::where('id', $id)->first();
			$winner->job = $job;
			// $winner->user = $user;
			// $bid = Bid::where('id', $bid_id)
			// 		->with('user')
			// 		->with('job')
			// 		->with('isApproved')
			// 		->first();
			return response()->json($job, 200);
		}
		return response()->json(['error' => true, 'message' => 'An error occured'], 500);
	}

	public function getBids($id) {
		$bids = Bid::info()->where('user_id', $id)
					->orderBy('created_at', 'desc')
					->paginate(env('BIDS_PER_PAGE'));
		return response()->json($bids);
	}

	public function getBidDetails($id, $bid_id) {
		$bid = Bid::info()->where([
				['id', '=', $bid_id],
				['user_id', '=', $id]
			])->first();

		return response()->json($bid);
	}

	protected function isBidded($id, $job_id) {
		$bid = Bid::where([
						['user_id', '=', $id],
						['job_id', '=', $job_id]
					])->first();
		if($bid) {
			return response()->json(['bidded' => true, 'bid' => $bid]);
		}
		return response()->json(['bidded' => false]);
	}
}
