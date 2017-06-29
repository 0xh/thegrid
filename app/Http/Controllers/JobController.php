<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Job;
use App\User;
use App\Bid;
use DB;

class JobController extends Controller
{
    public function add(Request $request) {
    	$data = $request->all();
    	$job = Job::create([
    		'user_id' => $data['user_id'],
            'name' => $data['name'],
            'category_id' => 1,
            'price' => $data['price'],
            'lat' => $data['lat'],
            'lng' => $data['lng'],
            'location' => $data['location'],
            'date' => date("Y-m-d H:i:s", strtotime($data['date']))
        ]);
    	$user = User::find($job->user_id);
    	$bids = Bid::where('job_id', $job->id)->get();
    	$job->user = $user;
    	$job->bids = $bids;
        return response()->json($job, 200);
    }

    public function getJobs($id) {
    	$jobs = Job::where('user_id', $id)
    				->with('bids')
    				->orderBy('created_at', 'desc')
    				->paginate(env('JOBS_PER_PAGE'));
    	return response()->json($jobs, 200);
    }

    public function getJobDetails($id, $job_id) {
    	$job = Job::where([
    			['user_id', '=', $id],
    			['id', '=', $job_id]
    		])->with('bidders')
    		->first();

    	return $job;
    }

    public function all(Request $request) {
    	$data = $request->all();
    	$f = sprintf("*, ( 3959 * acos(cos(radians(%f)) * cos(radians(lat)) * cos(radians(lng) - radians(%f)) + sin(radians(%f)) * sin(radians(lat))) ) AS distance ",
    		$data['lat'], $data['lng'], $data['lat']);
    	$jobs = Job::with('user')
    				->with('bids')
    				->whereDate('date', '>', date('Y-m-d'))
    				->select(DB::raw($f))
    				->having('distance', '<', 50)
    				->groupBy('distance')
    				->get();
    	return response()->json($jobs, 200);
    	
    	//$jobs = DB::table('jobs')->select(DB::raw($f))->having('distance', '<', 25)->groupBy('distance')->get();
    	//dd($jobs);
    	// return $jobs;
    	// SELECT 
		// id, 
		// (
		//    3959 *
		//    acos(cos(radians(25.2627866)) * 
		//    cos(radians(lat)) * 
		//    cos(radians(lng) - 
		//    radians(55.3267619)) + 
		//    sin(radians(25.2627866)) * 
		//    sin(radians(lat )))
		// ) AS distance 
		// FROM jobs 
		// HAVING distance < 25 
		// ORDER BY distance LIMIT 0, 30;
    }

    public function viewJob($id) {
    	$job = Job::find($id);
    	return response()->json($job, 200);
    }
}
