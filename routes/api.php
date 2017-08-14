<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/test', function(Request $request) {
	// $j = App\Job::where('id', 44)->with('skills')->first();
	// $j->skills()->attach([1,2]);
  $data = $request->all();
	return response()->json($data);
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
  $user = App\User::where('id', $request->user()->id)
                  ->withCount('jobs')
                  ->withCount('bids')
                  ->with('profile')
                  ->first();
  return response()->json($user);
});


Route::post('/login', 'Auth\LoginController@loginAPI');
Route::post('/login/{provider}', 'Auth\RegisterController@handleProviderCallbackAPI');


Route::post('/password/email', 'Auth\ForgotPasswordController@getResetToken');
Route::post('/password/reset', 'Auth\ResetPasswordController@reset');
Route::post('/users/register', 'Auth\RegisterController@register');
Route::post('/users/confirmation/{token}', 'Auth\RegisterController@confirmation');
Route::get('/users/skills', 'SkillController@getSkills');

Route::post('/account/verify', 'Auth\RegisterController@submitCode');
Route::post('/account/verify/resend', 'Auth\RegisterController@resendCode');

Route::post('/register/step1', 'Auth\RegisterController@validateStep1');

Route::get('/users/unique/{input}', 'UserController@isUnique');

Route::get('/jobs/categories', 'JobCategoryController@getCategories');
Route::get('/jobs/search', 'JobController@search');
Route::get('/jobs/{id}', 'JobController@getUserJob');

Route::middleware('auth:api')->prefix('users')->group(function() {
  Route::post('/', function($id) {
    // $user = request()->user();
    // $user->profile = $user->profile;
    // return response()->json($user);
  });

  Route::get('/test', function(Request $request) {
    $user = $request->user();
    $user->profile = $request->user()->profile;
    return response()->json($user);
  });

  Route::get('/{id}', 'UserController@getUser');
  Route::put('/{id}', 'UserController@updateUser');
  Route::post('/{id}/upload', 'UserController@upload');

  Route::get('/{id}/jobs', 'JobController@getJobs');
  Route::post('/{id}/jobs', 'JobController@add');
  Route::get('/{id}/jobs/{job_id}', 'JobController@getJobDetails');

  // Route::post('/bid', 'BidController@bid');
  Route::post('/bid/approve', 'BidController@approveBid');
  Route::get('/{id}/bids', 'BidController@getBids');
  Route::post('/{id}/bids', 'BidController@bid');
  Route::get('/{id}/bids/{bid_id}', 'BidController@getBidDetails');
  Route::get('/{id}/bid/{bid_id}', 'BidController@getBidDetails');
  Route::get('/{id}/bid/check/{job_id}', 'BidController@isBidded');

  Route::get('/{id}/conversations', 'ConversationController@getConversations');
  Route::post('/{id}/conversations', 'ConversationController@createConversation');
  Route::get('/{id}/conversations/{conversation_id}', 'MessageController@getMessages');
  Route::post('/{id}/conversations/{conversation_id}', 'MessageController@createMessage');

  Route::get('/{id}/skills', 'UserController@getSkills');
  Route::post('/{id}/skills', 'UserController@addSkill');
  Route::delete('/{id}/skills/{skill_id}', 'UserController@removeSkill');

});
