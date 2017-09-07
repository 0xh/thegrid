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

		if(isset($request->files)) {
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
			$skills = $data['skills'];
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
		$jobs = Job::with('user')
					->with('bids')
					->whereDate('date', '>', date('Y-m-d'))
					//->select(DB::raw($f))
					// ->having('distance', '<', 50)
					//->groupBy('distance')
					->get();
		return response()->json($jobs, 200);
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
			return response()->json($job);
		}

		return response()->json(['status' => 'failed', 'message' => 'Something went wrong']);
	}
}
