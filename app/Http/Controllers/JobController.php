<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Job;
use App\JobFile;
use App\User;
use App\Bid;
use App\Transaction;
use App\FlagJob;
use App\View;
use App\Search;
use DB;
use Cache;
use App\Notifications\AwardBid;
use App\Notifications\MarkPostInProgress;
use App\Notifications\MarkPostReview;
use App\Notifications\MarkPostComplete;
use OneSignal;
use Carbon\Carbon;

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
			'country_id' => $data['country_id'],
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

		// if( isset($data['skills'])) {
			
		// 	$skills = json_decode($data['skills'], true);
		// 	if( is_array($skills) ) {
		// 		foreach($skills as $skill) {
		// 			$_job->skills()->attach($skill['id']);
		// 		}
		// 	}
		// }
		if( isset($data['tags'])) {
			
			$tags = json_decode($data['tags'], true);
			if( is_array($tags) ) {
				foreach($tags as $tag) {
					$_job->tags()->attach($tag['id']);
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
			'country_id' => $data['country_id'],
			'lat' => $data['lat'],
			'lng' => $data['lng'],
			'location' => $data['location'],
			'details' => $data['details'],
			'date' => date("Y-m-d H:i:s", strtotime($data['date']))
		]);

		$_job = Job::where('id', $data['id'])->with('files')->first();

		if(isset($data['_files'])) {
			foreach($_job->files as $_file) {

				$inFiles = false;

				foreach($data['_files'] as $file) {
					$file = json_decode($file, true);
					if(isset($file['id'])) {
						if($_file->id == $file['id']) {
							$inFiles = true;
						}
					}
				}

				if(!$inFiles) {
					JobFile::where('id', $_file->id)->update(['status' => 1]);
				}
			}
			
		} else {
			JobFile::where('job_id', $_job->id)->update(['status' => 1]);
		}

		if($request->file('files')) {
			Storage::disk('public_uploads')->makeDirectory('posts');
			foreach($request->file('files') as $file) {
				$new_file['path'] = $file->store('posts', 'public_uploads');
				$new_file['name'] = $file->hashName();
				$new_file['original_name'] = $file->getClientOriginalName();
				$new_file['file_size'] = $file->getClientSize();
				$new_file['type'] = $file->extension();
				$new_file['job_id'] = $_job->id;
				JobFile::create($new_file);
			}
		}

		// if( isset($data['skills'])) {
			
		// 	$skills = json_decode($data['skills'], true);
		// 	if( is_array($skills) ) {
		// 		$_job->skills()->detach();
		// 		foreach($skills as $skill) {
		// 			$_job->skills()->attach($skill['id']);
		// 		}
		// 	}
		// }

		if( isset($data['tags'])) {
			
			$tags = json_decode($data['tags'], true);
			if( is_array($tags) ) {
				$_job->tags()->detach();
				foreach($tags as $tag) {
					$_job->tags()->attach($tag['id']);
				}
			}
		}

		$job = Job::infoWithBidders()
			->with('viewsThisWeek', 'viewsThisMonth', 'offersThisWeek', 'offersThisMonth')
			->where('id', $data['id']);
		
		$user_id = $request->user()->id;
		// $job->with([ 'bid' => function($query) use($user_id) {
		// 	$query->where('user_id', $user_id);
		// }]);
			
		$job = $job->first();		
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
		$jobs = Job::infoWithBidders()
					->where('user_id', $this->id)
					->orderBy('updated_at', 'desc')
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

	public function getJobDetails(Request $request, $id, $job_id) {
		$job = Job::infoWithBidders()->where([
				['user_id', '=', $id],
				['id', '=', $job_id]
			])->first();

		return response()->json($job);
	}

	public function addViewJob($user, $job, $ip) {
		
		if($user) {
			if($user->id == $job->user_id) return;
		}

		View::create([
			'job_id' => $job->id,
			'user_id' => $user ? $user->id : null,
			'ip' => $ip
		]);
	}

	public function getUserJob(Request $request, $id) {
		if( $request->user() ) {
			$job = Job::info()->where('id', $id)->first();
			$bid = Bid::where([['job_id', '=', $id],['user_id', '=', $request->user()->id]])
				->first();
			$job->bid = $bid;
		} else {
			$job = Job::info()->where('id', $id)->first();
		}
		
		if($job) {

			$this->addViewJob($request->user(), $job, $request->ip());
			
			return response()->json($job);
		}
		
		return response()->json(['status' => 'failed'], 400);
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

		$jobs = Job::with('user');

		if($request->user()) {
			$user_id = $request->user()->id;
			$jobs->with('winner')
				->with([ 'bid' => function($query) use($user_id) {
					$query->where('user_id', $user_id);
				}]);
		} else {

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
		
		if(isset($data['category'])) {
			$jobs->where('category_id', $data['category']);
		}

		// if(!$request->user()) {
		// 	$jobs->where('is_awarded', false);
		// }
					
		$jobs->select(DB::raw($f))
		->having('distance', '<', $radius)
		->orderBy('distance', 'ASC');
		// ->groupBy('distance');
					// ->get();
		return response()->json($jobs->get(), 200);
	}

	public function getMarkerDetails(Request $request, $id, $job_id ) {
		$job = Job::with('user');
		if( $request->user() ) {
			$user_id = $request->user()->id;
			$job->with('winner')
				->with([ 'bid' => function($query) use($user_id) {
					$query->where('user_id', $user_id);
				}]);
		}

		$job->where('id', $job_id);

		return response()->json($job->first());
	}

	public function viewJob($id) {
		$job = Job::find($id);
		return response()->json($job, 200);
	}

	public function search(Request $request) {
		$data = $request->all();
		$q = $data['q'];

		$search = Job::with('user', 'bids', 'files')
			->leftJoin('job_tag', 'job_tag.job_id', '=', 'jobs.id')
			->leftJoin('sub_categories', 'job_tag.tag_id', '=', 'sub_categories.id')
			->whereDate('jobs.date', '>', Carbon::now())
			->where(function($query) use($q) {
				$query->where('jobs.name', 'like', "%{$q}%")
					->orWhere('jobs.location', 'like', "%{$q}%")
					->orWhere('sub_categories.name', 'like', "%{$q}%");
			})
			->groupBy('jobs.id');
		
		if($request->user()) {
			$search->with('winner');
		}

		if($data['lat'] && $data['lng']) {
			$f = sprintf("jobs.*, ( 6371 * acos(cos(radians(%f)) * cos(radians(lat)) * cos(radians(lng) - radians(%f)) + sin(radians(%f)) * sin(radians(lat))) ) AS distance ",
			$data['lat'], $data['lng'], $data['lat']);

			$search->select(DB::raw($f))
				// ->having('distance', '<', 50)
				->orderBy('distance', 'ASC');
		}

		$user_id = null;
		if( $request->user() ) {
			$user_id = $request->user()->id;
		}
		$this->saveSearch($q, $user_id);
		return response()->json($search->get());
	}

	public function saveSearch($q, $id) {
		Search::Create([
			'search' => $q,
			'user_id' => $id
		]);
	}
		
	public function setJobStatus(Request $request, $id, $job_id) {
		$data = $request->all();
		$job = Job::infoWithBidders()->where('id', $job_id)->first();

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
				$notification_data['type'] = 'bid';

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

	public function getRelatedJobs(Request $request, $id) {
		$user = $request->user();
  
		$user_tags = $user->tags;
		$_user_tags = [];

		foreach($user_tags as $tag) {
			array_push($_user_tags, $tag->id);
		}

		$related_jobs = Job::with('tags')
			->with('user', 'bids', 'winner', 'files')
			->whereHas('tags', function ($query) use($_user_tags){
				$query->whereIn('sub_categories.id', $_user_tags);
			})
			->where('user_id', '!=', $user->id)
			->where('is_awarded', '=', false)
			->whereDate('date', '>', Carbon::now())
			->take(5)
			->get();

		return response()->json($related_jobs);
	}
	
	public function getPopularJobs(Request $request, $id) {
		$user = $request->user();

		$popular_jobs = Job::withCount('views')
			->with('user', 'bids', 'winner', 'files')
			->where('user_id', '!=', $user->id)
			->where('is_awarded', false)
			->whereDate('date', '>', Carbon::now())
			->orderBy('views_count', 'desc')
			->take(5)
			->get();
		
		return response()->json($popular_jobs);
	}

	public function sendNotificationWithin1km($lat, $lng, $post_id, $username, $user_id) {

		$user = User::where('id', $user_id)->with('settings')->first();
		
		$settings = $user->settings->keyBy('name');

		if(isset($settings['notifications'])) {
			if($settings['notifications']['value'] != 1) {
				return;
			}
		}
		
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

	public function flag(Request $request, $id, $job_id) {
		$data = $request->all();

		$user_id = $request->user()->id;

		$flag = FlagJob::create([
			'job_id' => $job_id,
			'user_id' => $user_id,
			'reason' => $data['reason']
		]);

		$job = Job::info()->where('id', $job_id)->first();

		return response()->json($job);
	}

	public function delete(Request $request, $id, $job_id) {
		$job = Job::where('id', $job_id)->first();
		// must much job->user_id to $request->user()->id
		if( $request->user()->id == $job->user_id ) {
			// soft delete the job
			$job->delete();

			return response()->json(['status' => 'success', 'message' => 'Successfully deleted']);
		}

		return response()->json(['status' => 'failed', 'message' => 'Something went wrong'], 400);
	}
	
}
