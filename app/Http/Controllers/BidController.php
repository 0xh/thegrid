<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Job;
use App\Bid;
use App\User;
use App\Winner;
use App\BidFile;
use App\Transaction;
use App\Notifications\BidToPost;
use App\Notifications\AwardBid;
use OneSignal;
use Carbon\Carbon;

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

			$job = Job::where('id', $data['job_id'])->first();

			$timeFirst  = strtotime(date('Y-m-d H:i:s'));
			$timeSecond = strtotime($job->date);
			$differenceInSeconds = $timeSecond - $timeFirst;

			if($differenceInSeconds <= 10) {
				$date = new \DateTime($job->date);
				$date->add(new \DateInterval('PT'. (60 - $differenceInSeconds) .'S'));
				$job->date = $date;
				$job->save();
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

			$this->sendBidNotification($notifiable->id, $title, $bid->job->id);

			return response()->json($bid, 200);
		} else {
			return response()->json(['error' => true, 'message' => 'Already bidded'], 422);
		}
	}

	public function rebid(Request $request, $id, $bid_id) {
		$data = $request->all();

		if($data['bid']) {
			$bid = Bid::where('id', $data['bid_id'])->first();
			$bid->price_bid = $data['bid'];
			$bid->times += 1;
			$bid->save();

			$job = Job::where('id', $bid->job_id)->first();
			
			$timeFirst  = strtotime(date('Y-m-d H:i:s'));
			$timeSecond = strtotime($job->date);
			$differenceInSeconds = $timeSecond - $timeFirst;

			if($differenceInSeconds <= 10) {
				$date = new \DateTime($job->date);
				$date->add(new \DateInterval('PT'. (60 - $differenceInSeconds) .'S'));
				$job->date = $date;
				$job->save();
			}

			$bid = Bid::info()->where('id', $data['bid_id'])->first();

			$bid->differenceInSeconds = $differenceInSeconds;
			$bid->currentTime = date('Y-m-d H:i:s');

			return response()->json($bid);
		} else {
			return response()->json(['status' => 'failed', 'message' => 'Something went wrong'], 400);
		}
	}

	public function approveBid(Request $request, $id, $bid_id) {
		$data = $request->all();
			
		$bid = Bid::where('id', $bid_id)->first();
		$bid->status = 1;
		$bid->save();
		
		$job = Job::infoWithBidders()->where('id', $data['job_id'])->first();
		$job->status = 1;
		$job->is_awarded = true;
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
			'created_at' => Carbon::now()
		];

		$notifiable->notify( new AwardBid($notification_data) );

		$this->sendAwardedNotification($notifiable->id, $title, $bid_id);

		return response()->json($job);
		
	}

	public function cancelApproveBid(Request $request, $id, $bid_id) {
		$data = $request->all();
			
		$bid = Bid::where('id', $bid_id)->first();
		$bid->status = 0;
		$bid->save();
		
		$job = Job::infoWithBidders()->where('id', $data['job_id'])->first();
		$job->status = 0;
		$job->is_awarded = false;
		$job->save();

		$notifiable = User::where('id', $data['user_id'])->first();
		
		$title = $request->user()->name .' canceled the post \'' . $job->name . '\'.';

		$notification_data = [
			'bid_id' => $bid_id,
			'job_id' => $data['job_id'],
			'job_name' => $job->name,
			'bidder_id' => $notifiable->id,
			'bidder_name' => $notifiable->name,
			'title' => $title,
			'body' => '',
			'created_at' => Carbon::now()
		];

		$notifiable->notify( new AwardBid($notification_data) );

		$this->sendAwardedNotification($notifiable->id, $title, $bid_id);

		return response()->json($job);
		
	}

	public function acceptJob(Request $request, $id, $bid_id) {
		$user = $request->user();

		$winner = Winner::create([
			'bid_id' => $bid_id,
			'user_id' => $user->id,
			'job_id' => $request->job_id
		]);

		if( $winner ) {
			$bid = Bid::where('id', $bid_id)->first();
			$bid->status = 2;
			$bid->save();

			$job = Job::where('id', $request->job_id)->first();
			$job->is_accepted = true;
			$job->save();

			$bid = Bid::info()->where('id', $bid_id)->first();

			return response()->json($bid);
		}

		return response()->json(['error' => true, 'message' => 'An error occured'], 400);

	}

	public function declineJob(Request $request, $id, $bid_id) {
		$user = $request->user();

		$bid = Bid::where('id', $bid_id)->first();
		$bid->status = 0;
		$bid->save();

		$job = Job::where('id', $request->job_id)->first();
		$job->is_accepted = false;
		$job->is_awarded = false;
		$job->save();

		$bid = Bid::info()->where('id', $bid_id)->first();

		return response()->json($bid);

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

	public function setJobStatus(Request $request, $id, $bid_id) {
		$data = $request->all();
		$job = Job::infoWithBidders()->where('id', $data['job_id'])->first();

		$job->status = $data['status'];

		if( $request->is_moving ) {
			$job->is_moving = true;
		}
		if( $request->is_reviewed ) {
			$job->is_reviewed = true;
		}
		if( $request->is_completed ) {
			$job->is_completed = true;
		}

		$job->save();

		$bid = Bid::info()->where('id', $bid_id)->first();

		if($data['status'] == 2) {
			$this->sendBidNotification($job->user->id, 'Your post has been marked in progress', $job->id);
		} elseif( $data['status'] == 4) {
			$this->sendBidNotification($job->user->id, 'Your post has been marked completed', $job->id);
			Transaction::create([
				'supplier_id' => $job->winner->user->id,
				'customer_id' => $job->user->id,
				'amount' => $job->winner->bid->price_bid,
				'job_id' => $job->id,
				'bid_id' => $bid_id,
				'status' => '1',
				'payment_type' => 'cod'
			]);
		}


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

	public function sendAwardedNotification($user_id, $message, $bid_id) {
		// $author = User::where('id', $author_id)->first();

		$user = User::where('id', $user_id)->with('settings')->first();
		
		$settings = $user->settings->keyBy('name');

		if(isset($settings['notifications'])) {
			if($settings['notifications']['value'] != 1) {
				return;
			}
		}
		
		OneSignal::sendNotificationUsingTags(
			$message, 
			array(
				array('key' => 'user_id', 'relation' => '=', 'value' => $user_id,)
			),
			$url = env('APP_URL') . '/bids/' . $bid_id);
	}

	public function sendBidNotification($user_id, $message, $post_id) {

		$user = User::where('id', $user_id)->with('settings')->first();
		
		$settings = $user->settings->keyBy('name');

		if(isset($settings['notifications'])) {
			if($settings['notifications']['value'] != 1) {
				return;
			}
		}
		
		OneSignal::sendNotificationUsingTags(
			$message, 
			array(
				array('key' => 'user_id', 'relation' => '=', 'value' => $user_id,)
			),
			$url = env('APP_URL') . '/posts/' . $post_id);
	}
	

}
