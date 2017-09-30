<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Job;
use App\JobFile;
use App\User;
use App\Bid;
use App\Transaction;
use DB;
use Cache;
use App\Notifications\AwardBid;
use App\Notifications\MarkPostInProgress;
use App\Notifications\MarkPostReview;
use App\Notifications\MarkPostComplete;
use OneSignal;

class JobController extends Controller
{

	private $user_id = null;

	public function __construct(Request $request) {
		// parent::__construct();
		if($request->user()) {
			$this->user_id = $request->user()->id;
		}
	}

	public function add(Request $request, $id) {
		$data = $request->all();
		
		$user_id = $request->user()->id;

		$_job = Job::create([
			'user_id' => $user_id,
			'name' => $data['name'],
			'category_id' => $data['category_id'],
			'price' => $data['price'],
			'lat' => $data['lat'],
			'lng' => $data['lng'],
			'location' => $data['location'],
			'details' => $data['details'],
			'date' => date("Y-m-d H:i:s", strtotime($data['date']))
		]);

		if($request->file('files')) {
			Storage::disk('public_uploads')->makeDirectory('posts');
			foreach($request->file('files') as $file) {
				$_file['path'] = $file->store('posts', 'public_uploads');
				$_file['name'] = $file->hashName();
				$_file['original_name'] = $file->getClientOriginalName();
				$_file['file_size'] = $file->getClientSize();
				$_file['type'] = $file->extension();
				$_file['job_id'] = $_job->id;
				JobFile::create($_file);
			}
		}

		if( isset($data['skills'])) {
			
			$skills = json_decode($data['skills'], true);
			if( is_array($skills) ) {
				foreach($skills as $skill) {
					$_job->skills()->attach($skill['id']);
				}
			}
		}

		$job = Job::info()->where('id', $_job['id'])->first();		
		$bids = Bid::where('job_id', $_job['id'])->get();
		$job->bids = $bids;

		$this->sendNotificationWithin1km($data['lat'], $data['lng'], $job->id, $job->user->username, $user_id);

		return response()->json($job);

	}

	public function edit(Request $request, $id) {
		$data = $request->all();
		
		$user_id = $request->user()->id;

		$_job = Job::where('id', $data['id'])->update([
			'user_id' => $user_id,
			'name' => $data['name'],
			'category_id' => $data['category_id'],
			'price' => $data['price'],
			'lat' => $data['lat'],
			'lng' => $data['lng'],
			'location' => $data['location'],
			'details' => $data['details'],
			'date' => date("Y-m-d H:i:s", strtotime($data['date']))
		]);

		// if($request->file('files')) {
		// 	Storage::disk('public_uploads')->makeDirectory('posts');
		// 	foreach($request->file('files') as $file) {
		// 		$_file['path'] = $file->store('posts', 'public_uploads');
		// 		$_file['name'] = $file->hashName();
		// 		$_file['original_name'] = $file->getClientOriginalName();
		// 		$_file['file_size'] = $file->getClientSize();
		// 		$_file['type'] = $file->extension();
		// 		$_file['job_id'] = $_job->id;
		// 		JobFile::create($_file);
		// 	}
		// }

		// if( isset($data['skills'])) {
			
		// 	$skills = json_decode($data['skills'], true);
		// 	if( is_array($skills) ) {
		// 		foreach($skills as $skill) {
		// 			$_job->skills()->attach($skill['id']);
		// 		}
		// 	}
		// }

		$job = Job::info()->where('id', $data['id'])->first();		
		$bids = Bid::where('job_id', $data['id'])->get();
		$job->bids = $bids;

		// $this->sendNotificationWithin1km($data['lat'], $data['lng'], $job->id, $job->user->username, $user_id);

		return response()->json($job);

	}

	public function getJobs($id) {

		$this->id = $id;
		// $jobs = Cache::remember('jobs-'.$id, 15, function() {
		//   return Job::where('user_id', $this->id)
		//         // ->with('bids')
		//         ->orderBy('created_at', 'desc')
		//         ->paginate(env('JOBS_PER_PAGE'));
		// });
		$jobs = Job::infoWithBidders()->where('user_id', $this->id)
					->orderBy('created_at', 'desc')
					->paginate(env('JOBS_PER_PAGE'));
		return response()->json($jobs, 200);
	}

	public function getRecentJobs(Request $request, $id) {
		$user_id = $id;
		if($request->user()) {
			$user_id = $request->user()->id;
		}
		$jobs = Job::info()->where('user_id', $user_id)->limit(5)->orderBy('created_at', 'DESC')->get();

		return response()->json($jobs);

	}

	public function getJobDetails($id, $job_id) {
		$job = Job::infoWithBidders()->where([
				['user_id', '=', $id],
				['id', '=', $job_id]
			])->first();

		return $job;
	}

	public function getUserJob($id) {
		$job = Job::info()->where('id', $id)->first();
		if($job)
			return response()->json($job);
		
		return response()->json(['status' => 'failed'], 422);
	}

	public function all(Request $request) {

		$data = $request->all();
		// 3959 ML
		// 6371 KM
		$f = sprintf("*, ( 6371 * acos(cos(radians(%f)) * cos(radians(lat)) * cos(radians(lng) - radians(%f)) + sin(radians(%f)) * sin(radians(lat))) ) AS distance ",
			$data['lat'], $data['lng'], $data['lat']);

		$radius = 50;

		if(isset($data['rad'])) {
			$radius = $data['rad'];
		}

		$jobs = Job::with('user')->with('bids');

		if($request->user()) {
			$jobs->with('winner');
		}

		if(isset($data['from'])) {

			$_f = strtotime($data['from']);
			$from = date('Y-m-d H:i:s', $_f);
			
			if(isset($data['to'])) {
				$_t = strtotime($data['to']);
				$to = date('Y-m-d H:i:s', $_t);
	
				$jobs->whereBetween('date', [$from, $to]);
			} else {
				$jobs->whereDate('date', '>', $from);
			}
			
		} else {
			$jobs->whereDate('date', '>', date('Y-m-d H:i:s'));
		}

		if(isset($data['status'])) {
			if($data['status'] == 'm_p') {
				$jobs->where('user_id', $data['user_id']);
			} elseif($data['status'] == 'm_b') {
				$jobs->whereHas('bids', function ($query) use($data) {
					$query->where('user_id', $data['user_id']);
				});
			}
		}

		if(isset($data['price'])) {
			$jobs->where('price', '>=', $data['price']);
		}

		if(!$request->user()) {
			$jobs->where('status', 0);
		} else {
			// $jobs->whereHas('winner', function ($query) use($request) {
			// 	$query->where('user_id', $request->user()->id);
			// });
		}
					
		$jobs->select(DB::raw($f))
		->having('distance', '<', $radius)
		->orderBy('distance', 'ASC')
		->groupBy('distance');
					// ->get();
		return response()->json($jobs->get(), 200);
	}

	public function viewJob($id) {
		$job = Job::find($id);
		return response()->json($job, 200);
	}

	public function search(Request $request) {
		$q = $request->input('q');
		return Job::latest()
								->search($q)
								->whereDate('date', '>', date('Y-m-d H:i:s'))
								->get();
	}
		
	public function setJobStatus(Request $request, $id, $job_id) {
		$data = $request->all();
		$job = Job::infoWithBidders()->where('id', $job_id)->first();

		$job->status = $data['status'];
		$job->save();

		if($job) {

			if($job->winner) {

				$notifiable = User::where('id', $job->winner->user->id)->first();

				$notification_data = [
					'job_id' => $job->id,
					'job_name' => $job->name,
					'body' => '',
					'created_at' => \Carbon\Carbon::now()
				];

				$title = '';

				if( $data['status'] == 1) {
					// $title = $request->user()->name . ' awarded the post \'' . $job->name . '\' to you.';
				} elseif( $data['status'] == 2) {
					$title = $request->user()->name . ' marked the post \'' . $job->name . '\' as in progress.';
				} elseif( $data['status'] == 3) {
					$title = $request->user()->name . ' reviewed the post \'' . $job->name . '\'.';
				} elseif( $data['status'] == 4) {
					$title = $request->user()->name . ' marked the post \'' . $job->name . '\'. as completed.';
				}

				$bid_id = $job->winner->bid->id;

				$notification_data['bid_id'] = $bid_id;
				$notification_data['title'] = $title;

				if( $data['status'] == 2) {
					$notifiable->notify( new MarkPostInProgress($notification_data) );
				} elseif( $data['status'] == 3) {
					$notifiable->notify( new MarkPostReview($notification_data) );
				} elseif( $data['status'] == 4) {
					$notifiable->notify( new MarkPostComplete($notification_data) );
					Transaction::create([
						'supplier_id' => $job->winner->user->id,
						'customer_id' => $request->user()->id,
						'amount' => $job->winner->bid->price_bid,
						'job_id' => $job->id,
						'bid_id' => $bid_id,
						'status' => '1',
						'payment_type' => 'cod'
					]);
				}

				$this->sendNotification($notifiable->id, $title, $bid_id);
	
			}

			return response()->json($job);
		}

		return response()->json(['status' => 'failed', 'message' => 'Something went wrong']);
	}

	public function getCompletedJobs(Request $request, $id) {

		$user_id = $id;
		if($request->user()) {
			$user_id = $request->user()->id;
		}

		$job_completed = User::where('id', $user_id)->with('completedJobs')->first();
		
		return response()->json($job_completed);
	}

	public function sendNotification($user_id, $message, $bid_id) {
		// $author = User::where('id', $author_id)->first();
		
		OneSignal::sendNotificationUsingTags(
			$message, 
			array(
				array('key' => 'user_id', 'relation' => '=', 'value' => $user_id,)
			),
			$url = env('APP_URL') . '/bids/' . $bid_id);
	}

	public function sendNotificationWithin1km($lat, $lng, $post_id, $username, $user_id) {
		
		//Earth’s radius, sphere
		$R = 6378137;
		
		//offsets in meters
		$dn = 1000;
		$de = 1000;
	
		//Coordinate offsets in radians
		$dLat = $dn/$R;
		$dLng = $de/($R*cos(pi()*$lat/180));
	
		//OffsetPosition, decimal degrees
		$latAdd = $lat + $dLat * 180/pi();
		$lngAdd = $lng + $dLng * 180/pi();
		
		$latSub = $lat - $dLat * 180/pi();
		$lngSub = $lng - $dLng * 180/pi();

		OneSignal::sendNotificationUsingTags(
			'New post within your location', 
			array(
				array('key' => 'lat', 'relation' => '<', 'value' => $latAdd),
				array('key' => 'lat', 'relation' => '>', 'value' => $latSub),
				array('key' => 'lng', 'relation' => '<', 'value' => $lngAdd),
				array('key' => 'lng', 'relation' => '>', 'value' => $lngSub),
				array('key' => 'user_id', 'relation' => '!=', 'value' => $user_id),
			),
			$url = env('APP_URL') .'/@'. $username .'/posts/'. $post_id);
	}
	
}
