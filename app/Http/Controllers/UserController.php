<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Twilio\Rest\Client;
use App\User;
use App\Profile;
use App\Skill;
use App\Review;
use App\Job;
use App\Location;
use App\Notifications\MarkPostReview;

class UserController extends Controller
{
  public function getUserDetails($id) {
    return response()->json(request()->user());
  }

  protected $client;

  public function __construct() {
    $this->client = new Client(env('TWILIO_ACCOUNT_SID'), env('TWILIO_AUTH_TOKEN'));
  }

  public function getUser($id) {
    $user = User::where('id', $id)->first();

    return response()->json($user);
  }

  public function getUserInit(Request $request) {
    $user = User::info()->where('id', $request->user()->id)->first();
    $user->keyedLocations = $user->locations->keyBy('alias');
    return response()->json($user);
  }

  public function sendSMS() {
    $to = '+971582507859';
    $message = 'test message from laravel';
    $twilioNumber = env('TWILIO_NUMBER');
    try {
      $this->client->messages->create(
        $to,
        [
          "body" => $message,
          "from" => $twilioNumber
        ]
      );
    } catch (Exception $e) {
      var_dump($e);
      return false;
    }
  }

  public function updateUser(Request $request, $id) {
    $data = $request->all();
    // $data['profile']['user_id'] = $id;
    $data['profile']['birth_date'] = date("Y-m-d H:i:s", strtotime($data['profile']['birth_date']));
    $user = Profile::updateOrCreate(
      ['user_id' => $id],
      $data['profile']
    );
    return $user;
  }

  public function upload(Request $request, $id) {
    Storage::makeDirectory('public/avatars');
    $file_name = time().'-'.$id.'.'.$request->avatar->extension();
    $path = Storage::putFileAs(
      'public/avatars', $request->file('avatar'), $file_name
    );
    $data['profile_image_url'] = $file_name;
    $user = Profile::updateOrCreate(
      ['user_id' => $id],
      $data
    );
    return $user;
  }

  public function getSkills($id) {
    $skills =  User::where('id', $id)->first()->skills;
    return response()->json($skills);
  }

  public function addSkill(Request $request, $id) {
    $data = $request->all();
    $user = User::where('id', $id)->first();
    if(isset($data['skill'])) {
      $_skill = Skill::where('skill', $data['skill'])->first();
      if( !$_skill ) {
        $skill = Skill::create([
          'skill' => ucwords($data['skill'])
        ]);
        $user->skills()->attach($skill->id);
        return $skill;
      }
    }
    
    $user->skills()->attach($data['id']);
    return $user->skills;
  }

  public function removeSkill($id, $skill_id) {
    $user = User::where('id', $id)->first();
    $user->skills()->detach($skill_id);
    return $user->skills;
  }

  public function isUnique(Request $request, $input) {
    $data = $request->all();

    $email = Validator::make($data, [
      $input => 'required|unique:users',
    ]);

    $response = ["passes" => $email->passes() ? 1 : 0];
    return response()->json($response);
  }

  public function reviewUser(Request $request, $id) {

    $data = $request->all();

    $review = Review::Create([
      'user_id' => $data['user_id'],
      'job_id' => $data['job_id'],
      'review' => $data['review'],
      'stars' => $data['stars']
    ]);

    if($review) {
      $job = Job::infoWithBidders()->where('id', $data['job_id'])->first();
      $job->status = 3;
      $job->save();

      $notifiable = User::where('id', $job->winner->user->id)->first();
      
      $notification_data = [
        'job_id' => $job->id,
        'job_name' => $job->name,
        'body' => '',
        'created_at' => \Carbon\Carbon::now()
      ];

      $title = $request->user()->name . ' gave you ' . $data['stars'] . ' star(s) for the post \'' . $job->name . '\'.';

      $bid_id = $job->winner->bid->id;

      $notification_data['bid_id'] = $bid_id;
      $notification_data['title'] = $title;

      $notifiable->notify( new MarkPostReview($notification_data) );
      

      return response()->json($job);
    }

    return response()->json(['status' => 'failed', 'message' => 'Could not review user']);
  }

  public function updateGeneral(Request $request, $id) {
    $data = $request->all();

    $u = User::info()->where('id', $id)->first();

    if($u->username == $data['user']['username']) {
      $_user = User::where('id', $id)->update($data['user']);
      $user = Profile::updateOrCreate(
        ['user_id' => $id],
        $data['profile']
      );
      $user = User::info()->where('id', $id)->first();
      
      return response()->json($user);
    } else {

      $validator = Validator::make($data['user'], [
        'username' => 'required|apha_dash|unique:users'
      ]);

      if( $validator->passes() ) {
        $_user = User::where('id', $id)->update($data['user']);
        $user = Profile::updateOrCreate(
          ['user_id' => $id],
          $data['profile']
        );
        $user = User::info()->where('id', $id)->first();
        
        return response()->json($user);
      }

      return response()->json($validator->errors(), 422);
    }
    
  }

  public function updateAccountEmail(Request $request, $id) {
    $data = $request->all();

    $validator = Validator::make($data, [
      'email' => 'required|string|email|max:255|unique:users'
    ]);

    if($validator->passes()) {
      $_user = User::where('id', $id)->update($data);
      
      $user = User::info()->where('id', $id)->first();
      return response()->json($user);
    }

    return response()->json($validator->errors(), 422);

  }

  public function updateAccountPassword(Request $request, $id) {
    $data = $request->all();

    $u = User::where('id', $id)->first();

    if(!Hash::check($data['old_password'], $u->password)) {
      return response()->json(['status' => 'failed', 'message' => 'Please enter the correct password'], 422);
    }

    $validator = Validator::make($data, [
      'password' => 'required|string|min:6|confirmed'
    ]);

    if($validator->passes()) {
      $_user = User::where('id', $id)->update(['password' => bcrypt($data['password'])]);
      
      $user = User::info()->where('id', $id)->first();
      return response()->json($user);
    }

    return response()->json($validator->errors(), 422);

  }

  public function updateLocations(Request $request, $id) {
    $user_id = $request->user()->id;

    $data = $request->all();

    $home = $data['home'];
    $home['alias'] = 'home';
    $work = $data['work'];
    $work['alias'] = 'work';

    Location::updateOrCreate(
      ['user_id' => $user_id, 'alias' => 'home'],
      $home
    );

    Location::updateOrCreate(
      ['user_id' => $user_id, 'alias' => 'work'],
      $work
    );

    $user = User::info()->where('id', $user_id)->first();
    return response()->json($user);

  }

  public function markNotificationAsRead(Request $request, $id) {

    $notification = $request->user()->notifications()->where('id',$request->id)->first();
    if($notification) {
      $notification->markAsRead();
      return response()->json(['status' => 'ok']);
    }

    return response()->json(['status' => 'failed'], 422);
  }

}
