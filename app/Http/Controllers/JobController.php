<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Job;
use App\JobFile;
use App\User;
use App\Bid;
use DB;
use Cache;
use App\Notifications\AwardBid;
use App\Notifications\MarkPostInProgress;
use App\Notifications\MarkPostReview;
use App\Notifications\MarkPostComplete;

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
		

		$_job = Job::create([
			'user_id' => $id,
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
		$f = sprintf("*, ( 3959 * acos(cos(radians(%f)) * cos(radians(lat)) * cos(radians(lng) - radians(%f)) + sin(radians(%f)) * sin(radians(lat))) ) AS distance ",
			$data['lat'], $data['lng'], $data['lat']);

		$radius = 50;

		if(isset($data['rad'])) {
			$radius = $data['rad'];
		}

		$jobs = Job::with('user')->with('bids');

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
			$jobs->whereDate('date', '>', date('Y-m-d'));
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
					
		$jobs->select(DB::raw($f))
		->having('distance', '<', $radius)
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
								->whereDate('date', '>', date('Y-m-d'))
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
				}
	

			}

			


			return response()->json($job);
		}

		return response()->json(['status' => 'failed', 'message' => 'Something went wrong']);
	}
}
