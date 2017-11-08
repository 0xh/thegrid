<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Route::get('/.well-known/acme-challenge/-SrI5nSyUR-1d2yAkt4fwl7Oqou9sfl4xYAvRRp0DsA', function() {
// 	return '-SrI5nSyUR-1d2yAkt4fwl7Oqou9sfl4xYAvRRp0DsA.sm-GIm2rFDV7UPT38bE9z5Jd2FbUBYk-zzf-f3YFR4k';
// });

use Illuminate\Http\Request;

Route::get('/', function(Request $request) {
	return App\Job::where('id', 123)->with('tags')->get();
});

Route::get('tags/{q}', function ($q) {
	

		// we only want "posts" table fields for Eloquent model, thus "selectRaw"
		$posts = App\Job::selectRaw('jobs.*')
				->leftJoin('job_tag', 'job_tag.job_id', '=', 'jobs.id')
				->leftJoin('sub_categories', 'job_tag.tag_id', '=', 'sub_categories.id')
				->where('jobs.name', 'like', "%{$q}%")
				->orWhere('jobs.location', 'like', "%{$q}%")
				->orWhere('sub_categories.name', 'like', "%{$q}%")
				->whereDate('date', '>', date('Y-m-d H:i:s'))
				->groupBy('jobs.id')
				->get();
		return $posts;
});

// Route::get('/', function () {
// 	return view('polymer');
// })->name('home');


// Route::get('/users', function() {
// 	$users = App\User::all();

// 	// foreach ($users as $user) {
// 	// 	echo $user->name . ' : ' . $user->items().join(',');
// 	// }
// 	return response()->json(request()->user());
// })->middleware('auth:api');

// Route::get('/sendsms', 'UserController@sendSMS');

// Route::get('/grid-elements', 'ElementsController@index');
// Route::get('/grid-elements/{element}', 'ElementsController@element');

// Route::post('/test', function(Request $request) {
// 	// $j = App\Job::where('id', 44)->with('skills')->first();
// 	// $j->skills()->attach([1,2]);

// 	return $request->all();
// });

// Auth::routes();

// Route::get('/bower_components/polymer/bundles', 'UserController@min');
// Route::get('/home', 'HomeController@index')->name('home');

// Route::get('/auth/{provider}', 'Auth\RegisterController@redirectToProvider');
// Route::get('/auth/{provider}/callback', 'Auth\RegisterController@handleProviderCallback');
// Route::get('/user/confirmation/{token}', 'Auth\RegisterController@confirmation')->name('confirmation');
// Route::post('/submitCode', 'Auth\RegisterController@submitCode');
// Route::post('/resendCode', 'Auth\RegisterController@resendCode');

// Route::get('/users/{id}', 'UserController@getUser');
// Route::put('/users/{id}', 'UserController@updateUser');

// Route::post('/job', 'JobController@add');
Route::get('/job/all', 'JobController@all');
// Route::get('/job/{id}', 'JobController@viewJob');
// Route::get('/{id}/jobs', 'JobController@getJobs');
// Route::get('/{id}/job/{job_id}', 'JobController@getJobDetails');

// Route::post('/bid', 'BidController@bid');
// Route::post('/bid/approve', 'BidController@approveBid');
// Route::get('/{id}/bids', 'BidController@getBids');
// Route::get('/{id}/bid/{bid_id}', 'BidController@getBidDetails');
// Route::get('/{id}/bid/check/{job_id}', 'BidController@isBidded');

// Route::get('/country/{iso}', 'CountryController@getCountryByISO');

// Route::get('/users/{id}/conversations', 'ConversationController@getConversations');
// Route::post('/users/{id}/conversations', 'ConversationController@createConversation');
// Route::get('/users/{id}/conversations/{conversation_id}', 'MessageController@getMessages');
// Route::post('/users/{id}/conversations/{conversation_id}', 'MessageController@createMessage');
