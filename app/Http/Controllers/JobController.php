<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Job;
use App\User;
use App\Bid;

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
        return $job;
    }

    public function getJobs($id) {
    	return Job::where('user_id', $id)
    				->with('bids')
    				->orderBy('created_at', 'desc')
    				->get();
    }

    public function all() {
    	$jobs = Job::with('user')->with('bids')->get();
    	return $jobs;
    }

    public function viewJob($id) {
    	return Job::find($id);
    }
}
