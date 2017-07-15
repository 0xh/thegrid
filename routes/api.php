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

Route::middleware('auth:api')->get('/user', function (Request $request) {
  $user = $request->user();
  $user->profile = $request->user()->profile;
  return response()->json($user);
});

Route::middleware('auth:api')->prefix('users')->group(function() {
  Route::post('/', function($id) {
    // $user = request()->user();
    // $user->profile = $user->profile;
    // return response()->json($user);
  });
  Route::get('/{id}', 'UserController@getUser');
  Route::put('/{id}', 'UserController@updateUser');

  Route::get('/{id}/jobs', 'JobController@getJobs');
  Route::get('/{id}/jobs/{job_id}', 'JobController@getJobDetails');
  Route::post('/{id}/jobs', 'JobController@add');

  // Route::post('/bid', 'BidController@bid');
  Route::post('/bid/approve', 'BidController@approveBid');
  Route::get('/{id}/bids', 'BidController@getBids');
  Route::post('/{id}/bids', 'BidController@bid');
  Route::get('/{id}/bid/{bid_id}', 'BidController@getBidDetails');
  Route::get('/{id}/bid/check/{job_id}', 'BidController@isBidded');

  Route::get('/{id}/conversations', 'ConversationController@getConversations');
  Route::post('/{id}/conversations', 'ConversationController@createConversation');
  Route::get('/{id}/conversations/{conversation_id}', 'MessageController@getMessages');
  Route::post('/{id}/conversations/{conversation_id}', 'MessageController@createMessage');
});
