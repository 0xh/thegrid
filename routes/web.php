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

Route::get('/', function () {
	return view('polymer');
})->name('home');


Route::get('/users', function() {
	$users = App\User::all();

	// foreach ($users as $user) {
	// 	echo $user->name . ' : ' . $user->items().join(',');
	// }
	return response()->json(request()->user());
})->middleware('auth:api');

Route::get('/sendsms', 'UserController@sendSMS');

Route::get('/grid-elements', 'ElementsController@index');
Route::get('/grid-elements/{element}', 'ElementsController@element');

Route::get('/test', function() {
	// echo gethostbyname("api.thegrid.com");
	// var_export (dns_get_record ( "api.thegrid.com") );
	//
	// $skills = App\Skill::with('usersCount')->get();//->with('users');
	//return $skills;
	// foreach ($skills as $skill) {
	// 	echo $skill->skill . ' : ' . $skill->usersCount .' <br/ >';
	// }
	// return App\User::all();


	try {
		$http = new \GuzzleHttp\Client(['headers' => [
			'Content-Type' => 'application/json'
		]]);
		$response = $http->post(env('APP_API_URL') . '/oauth/token/', [

				// 'body' => json_encode([
				// 		'grant_type' => 'password',
				// 		'client_id' => env('CLIENT_ID'),
				// 		'client_secret' => env('CLIENT_SECRET'),
				// 		'username' => 'zachary35@example.com', /// or any other network that your server is able to resolve.
				// 		'password' => 'secret',
				// ])
		]);
   }
   catch (\GuzzleHttp\Exception\ClientException $e) {
        $response = $e->getResponse();
        $result =  $response->getBody();

        //do something with it....
				var_dump($response);
   }



	// curl -d '{"grant_type":"password", "client_id":"2", "client_secret":"xIxcFxQGFbYkSXKmd0MQ4iuC8Ejfw8x6EcuZJpHe","username":"zachary35@example.com","password":"secret"}' -H "Content-Type: application/json" -X POST http:api.thegrid.com/oauth/token/
});

Auth::routes();

Route::get('/bower_components/polymer/bundles', 'UserController@min');
Route::get('/home', 'HomeController@index')->name('home');

Route::get('/auth/{provider}', 'Auth\RegisterController@redirectToProvider');
Route::get('/auth/{provider}/callback', 'Auth\RegisterController@handleProviderCallback');
Route::get('/user/confirmation/{token}', 'Auth\RegisterController@confirmation')->name('confirmation');
Route::post('/submitCode', 'Auth\RegisterController@submitCode');
Route::post('/resendCode', 'Auth\RegisterController@resendCode');

Route::get('/users/{id}', 'UserController@getUser');
Route::put('/users/{id}', 'UserController@updateUser');

Route::post('/job', 'JobController@add');
Route::get('/job/all', 'JobController@all');
Route::get('/job/{id}', 'JobController@viewJob');
Route::get('/{id}/jobs', 'JobController@getJobs');
Route::get('/{id}/job/{job_id}', 'JobController@getJobDetails');

// Route::post('/bid', 'BidController@bid');
// Route::post('/bid/approve', 'BidController@approveBid');
// Route::get('/{id}/bids', 'BidController@getBids');
// Route::get('/{id}/bid/{bid_id}', 'BidController@getBidDetails');
// Route::get('/{id}/bid/check/{job_id}', 'BidController@isBidded');

Route::get('/country/{iso}', 'CountryController@getCountryByISO');

// Route::get('/users/{id}/conversations', 'ConversationController@getConversations');
// Route::post('/users/{id}/conversations', 'ConversationController@createConversation');
// Route::get('/users/{id}/conversations/{conversation_id}', 'MessageController@getMessages');
// Route::post('/users/{id}/conversations/{conversation_id}', 'MessageController@createMessage');
