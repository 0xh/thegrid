<?php

use Illuminate\Http\Request;
use App\Job;
use App\User;

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

Route::get('/app-version', 'HomeController@getAppVersion');

Route::get('/test', function(Request $request) {
	// $j = App\Job::where('id', 44)->with('skills')->first();
	// $j->skills()->attach([1,2]);
  // $data = $request->all();
  // return response()->json($data);
  // $date = new DateTime('2017-09-17 10:38:54');
  // $date->add(new DateInterval('PT10S'));
  // echo $date->format('Y-m-d H:i:s') . "\n";
  // echo date('Y-m-d H:i:s') . "\n";
  // echo strtotime(date('Y-m-d H:i:s'));
  // $message = 'aaaa';
  // $user_id =  7;
  // $content = array(
  //     "en" => "$message"
  // );

  // $fields = array(
  //     'app_id' => "4ab53490-5824-4613-8c96-9484221bf6db",
  //     'filters' => array(array("field" => "tag", "key" => "user_id", "relation" => "=", "value" => "$user_id")),
  //     'contents' => $content
  // );

  // $fields = json_encode($fields);
  // print("\nJSON sent:\n");
  // print($fields);

  // $ch = curl_init();
  // curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
  // curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
  //     'Authorization: Basic NWQxMGIwNmYtNzE1Yy00Y2E5LTlhYjUtZjFhMjEyMDVmMzBk'));
  // curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  // curl_setopt($ch, CURLOPT_HEADER, FALSE);
  // curl_setopt($ch, CURLOPT_POST, TRUE);
  // curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
  // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

  // $response = curl_exec($ch);
  // curl_close($ch);

  // dd($response);
  // $d = OneSignal::sendNotificationToAll("Some Message", $url = null, $data = null, $buttons = null, $schedule = null);
  // $d = OneSignal::sendNotificationToUser("Some Message", '449329ac-9c31-456d-abfc-54bf8d54c188', $url = null, $data = null, $buttons = null, $schedule = null);
  // $d = OneSignal::sendNotificationUsingTags("Some Message", array(array("key" => "user_id", "relation" => "=", "value" => 2)), $url = null, $data = null, $buttons = null, $schedule = null);

  // $heading = array(
  //   'en' => 'Testing'
  // );
  
  // OneSignal::setParam('headings', $heading)->sendNotificationUsingTags(
  //   'Hello', 
  //   array(
  //     array('key' => 'user_id', 'relation' => '=', 'value' => 2,)
  //   ),
  //   $url = env('APP_URL') . '/inbox/' . 2);

  // $lat = 25.16007598042906;
  // $lng = 55.23407940422977;
 
  // //Earth’s radius, sphere
  // $R=6378137;
 
  // //offsets in meters
  // $dn = 1000;
  // $de = 1000;
 
  // //Coordinate offsets in radians
  // $dLat = $dn/$R;
  // $dLng = $de/($R*cos(pi()*$lat/180));
 
  // //OffsetPosition, decimal degrees
  // $latAdd = $lat + $dLat * 180/pi();
  // $lngAdd = $lng + $dLng * 180/pi();
  
  // $latSub = $lat - $dLat * 180/pi();
  // $lngSub = $lng - $dLng * 180/pi();

  // echo $latAdd .'<br/>'. $lngAdd;
  // echo '<br/>';
  // echo $latSub .'<br/>'. $lngSub;
  // // dd($d);

  // OneSignal::sendNotificationUsingTags(
  //   'New post within your location', 
  //   array(
  //     array('key' => 'lat', 'relation' => '<', 'value' => $latAdd),
  //     array('key' => 'lat', 'relation' => '>', 'value' => $latSub),
  //     array('key' => 'lng', 'relation' => '<', 'value' => $lngAdd),
  //     array('key' => 'lng', 'relation' => '>', 'value' => $lngSub),
  //     array('key' => 'user_id', 'relation' => '!=', 'value' => 2),
      
  //   ),
  //   $url = env('APP_URL') .'/@macbook/posts/111');

  // $post = App\Job::test()
  //   ->with('viewsThisWeek')
  //   ->where('user_id', '7')->orderBy('id', 'desc')->paginate(5);
  // $post = App\View::where('job_id', 123)
  //   ->where('created_at', '>=', \Carbon\Carbon::now()->subWeeks(1))
  //   ->get()
  //   ->groupBy(function($item){ return $item->created_at->format('Y-m-d H:i:s'); });
  // return $post;
});

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//   $user = App\User::where('id', $request->user()->id)
//                   ->withCount('jobs')
//                   ->withCount('bids')
//                   ->with('profile')
//                   ->with('reviews')
//                   ->first();
//   return response()->json($user);
// });
Route::get('/get_server_time', function() {
  $server_time = ['date' => date('Y-m-d H:i:s')];

  return response()->json($server_time);
});

Route::get('/opengraph/{id}', 'HomeController@getOG');
Route::get('/getlocationbyip', 'HomeController@getLocationByIp');
Route::get('/users/getlocationbyip', 'HomeController@getLocationByIp')->middleware('auth:api');

Route::get('/job/all', 'JobController@all');

Route::get('/user', 'UserController@getUserInit')->middleware('auth:api');

Route::post('/login', 'Auth\LoginController@loginAPI');
Route::post('/login/{provider}', 'Auth\RegisterController@handleProviderCallbackAPI');

Route::post('/password/email', 'Auth\ForgotPasswordController@getResetToken');
Route::post('/password/reset', 'Auth\ResetPasswordController@reset');
Route::post('/users/register', 'Auth\RegisterController@register');
Route::post('/users/confirmation/{token}', 'Auth\RegisterController@confirmation');
Route::get('/users/skills', 'SkillController@getSkills');
Route::get('/users/reviews/{id}', 'UserController@getReviews');
Route::post('/users/feedback', 'UserController@newFeedback');

Route::get('/users/{username}', 'UserController@getUserByUsername');
Route::get('/recent/jobs/{id}', 'JobController@getRecentJobs');
Route::get('/completed/jobs/{id}', 'JobController@getCompletedJobs');

Route::post('/account/verify', 'Auth\RegisterController@submitCode');
Route::post('/account/verify/resend', 'Auth\RegisterController@resendCode');

Route::post('/register/step1', 'Auth\RegisterController@validateStep1');

Route::get('/users/unique/{input}', 'UserController@isUnique');

Route::get('/jobs/categories', 'JobCategoryController@getCategories');
Route::get('/jobs/search', 'JobController@search');
Route::get('/jobs/{id}', 'JobController@getUserJob');
Route::get('/tags', 'TagController@getAllTags');

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

  // Route::get('/getlocationbyip', 'HomeController@getLocationByIp');

  Route::get('/job/all', 'JobController@all');
  Route::get('/jobs/search', 'JobController@search');

  Route::get('/{id}', 'UserController@getUser');
  Route::put('/{id}', 'UserController@updateUser');
  Route::post('/{id}/upload', 'UserController@upload');

  Route::get('/{id}/jobs', 'JobController@getJobs');
  Route::post('/{id}/jobs', 'JobController@add');
  Route::post('/{id}/jobs/edit', 'JobController@edit');
  Route::get('/{id}/jobs/{job_id}', 'JobController@getJobDetails');
  Route::post('/{id}/jobs/{job_id}/status', 'JobController@setJobStatus');
  Route::post('/{id}/jobs/{job_id}/flag', 'JobController@flag');
  Route::get('/jobs/{id}/view', 'JobController@getUserJob');
  Route::post('{id}/jobs/{job_id}/delete', 'JobController@delete');
  Route::post('{id}/jobs/{job_id}/query', 'QueryController@create');
  Route::post('{id}/queries/{query_id}/like', 'QueryController@like');
  Route::post('{id}/queries/{query_id}/dislike', 'QueryController@dislike');
  Route::post('{id}/queries/{query_id}/reply', 'QueryController@reply');
  
  Route::get('/{id}/recent/jobs', 'JobController@getRecentJobs');
  Route::get('/{id}/completed/jobs', 'JobController@getCompletedJobs');
  Route::get('/{id}/reviews', 'UserController@getReviews');

  // Route::post('/bid', 'BidController@bid');
  // Route::post('/{id}/bids/approve', 'BidController@approveBid');
  Route::get('/{id}/bids', 'BidController@getBids');
  Route::post('/{id}/bids', 'BidController@bid');
  Route::get('/{id}/bids/highlighted', 'BidController@getHighlighted');
  Route::get('/{id}/bids/{bid_id}', 'BidController@getBidDetails');
  Route::get('/{id}/bid/{bid_id}', 'BidController@getBidDetails');
  Route::get('/{id}/bid/check/{job_id}', 'BidController@isBidded');
  Route::post('/{id}/bids/{bid_id}/approve', 'BidController@approveBid');
  Route::post('/{id}/bids/{bid_id}/cancel', 'BidController@cancelApproveBid');
  Route::post('/{id}/bids/{bid_id}/status', 'BidController@setJobStatus');
  Route::post('/{id}/bids/{bid_id}/rebid', 'BidController@rebid');
  Route::post('/{id}/bids/{bid_id}/accept', 'BidController@acceptJob');
  Route::post('/{id}/bids/{bid_id}/decline', 'BidController@declineJob');
  Route::post('/{id}/bids/{bid_id}/remove', 'BidController@removeBid');
  Route::post('/{id}/bids/{bid_id}/highlight', 'BidController@highlight');

  Route::get('/{id}/conversations', 'ConversationController@getConversations');
  Route::get('/{id}/conversations', 'ConversationController@getConversations');
  Route::post('/{id}/conversations', 'ConversationController@createConversation');
  Route::get('/{id}/conversations/{conversation_id}', 'MessageController@getMessages');
  Route::post('/{id}/conversations/{conversation_id}', 'MessageController@createMessage');
  Route::post('/{id}/conversations/{conversation_id}/read', 'MessageController@readMessage');  

  Route::get('/{id}/inbox', 'ConversationController@getConversations');
  Route::get('/{id}/inbox', 'ConversationController@getConversations');
  Route::post('/{id}/inbox', 'ConversationController@createConversation');
  Route::get('/{id}/inbox/{conversation_id}', 'MessageController@getMessages');
  Route::post('/{id}/inbox/{conversation_id}', 'MessageController@createMessage');

  Route::get('/{id}/skills', 'UserController@getSkills');
  Route::post('/{id}/skills', 'UserController@addSkill');
  Route::delete('/{id}/skills/{skill_id}', 'UserController@removeSkill');

  Route::get('/{id}/tags', 'UserController@getTags');
  Route::post('/{id}/tags', 'UserController@addTag');
  Route::delete('/{id}/tags/{tag_id}', 'UserController@removeTag');

  Route::post('/{id}/review', 'UserController@reviewUser');

  Route::post('/{id}/settings/general', 'UserController@updateGeneral');
  Route::post('/{id}/settings/account/email', 'UserController@updateAccountEmail');
  Route::post('/{id}/settings/account/password', 'UserController@updateAccountPassword');
  Route::post('/{id}/settings/locations', 'UserController@updateLocations');
  Route::delete('/{id}/settings/locations/{location_id}', 'UserController@deleteLocation');
  Route::post('/{id}/settings/mobile', 'UserController@updateMobile');
  Route::post('/{id}/settings/privacy', 'UserController@updateSetting');
  Route::post('/{id}/settings/notifications', 'UserController@updateSetting');
  Route::post('/{id}/settings/name', 'UserController@updateName');

  Route::post('/{id}/profile/about', 'UserController@updateBio');


  Route::get('/{id}/notifications', function(Request $request, $id) {
    $notifications = $request->user()->unreadNotifications->groupBy('data.job_id');

    return response()->json($notifications);
  });

  Route::post('/{id}/notifications/read', 'UserController@markNotificationAsRead');

  Route::post('/{id}/email/verification/resend', 'UserController@resendEmail');


  Route::get('/{id}/activity/logs', 'UserController@getActiviyLogs');

  Route::get('/{id}/feedback', 'UserController@getMyFeedback');
  Route::post('/{id}/feedback', 'UserController@newFeedback');
  
  Route::post('/{id}/transactions', 'UserController@newTransaction');
  Route::get('/{id}/transactions', 'UserController@getTransactions');


});
