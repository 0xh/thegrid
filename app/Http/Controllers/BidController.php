<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Job;
use App\Bid;
use App\User;
use App\Winner;
use App\BidFile;
use App\Notifications\BidToPost;
use App\Notifications\AwardBid;

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

			if($request->file('files')) {
				Storage::disk('public_uploads')->makeDirectory('bids');
				foreach($request->file('files') as $file) {
					$_file['path'] = $file->store('bids', 'public_uploads');
					$_file['name'] = $file->hashName();
					$_file['original_name'] = $file->getClientOriginalName();
					$_file['file_size'] = $file->getClientSize();
					$_file['type'] = $file->extension();
					$_file['bid_id'] = $bid->id;
					BidFile::create($_file);
				}
			}

			// $user = User::where('id', $id)->first();
			// $job = Job::info()->where('id', $data['job_id'])->first();
			// $bid->job = $job;
			// $bid->user = $user;
			$bid = Bid::info()->where('id', $bid->id)->first();

			$notifiable = User::where('id', $bid->job->user_id)->first();
			
			$other_bidder_count = count($bid->job->only_bids) - 1;

			if($other_bidder_count < 1) {
				$title = $request->user()->name . ' bids on your post \'' . $bid->job->name .'\'';
			} else {
				$title = $request->user()->name . ' and ' . $other_bidder_count . ' others bid on your post \'' . $bid->job->name .'\'';
			}

			$notification_data = [
				'bid_id' => $bid->id,
				'job_id' => $bid->job->id,
				'job_name' => $bid->job->name,
				'bidder_id' => $notifiable->id,
				'bidder_name' => $notifiable->name,
				'other_bidder_count' => count($bid->job->only_bids) - 1,
				'title' => $title,
				'body' => '',
				'created_at' => \Carbon\Carbon::now()
			];

			$notifiable->notify( new BidToPost($notification_data) );

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

			$notifiable = User::where('id', $data['user_id'])->first();
			
			$title = $request->user()->name .' awarded the post \'' . $job->name . '\' to you.';

			$notification_data = [
				'bid_id' => $bid_id,
				'job_id' => $data['job_id'],
				'job_name' => $job->name,
				'bidder_id' => $notifiable->id,
				'bidder_name' => $notifiable->name,
				'title' => $title,
				'body' => '',
				'created_at' => \Carbon\Carbon::now()
			];

			$notifiable->notify( new AwardBid($notification_data) );


			return response()->json($job);
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
