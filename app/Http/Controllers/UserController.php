<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Twilio\Rest\Client;
use Mail;
use App\User;
use App\Profile;
use App\Skill;
use App\Review;
use App\Job;
use App\Location;
use App\Setting;
use App\Feedback;
use App\Transaction;
use App\Notifications\MarkPostReview;
use Spatie\Activitylog\Models\Activity;
use OneSignal;

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
    $user->keyedSettings = $user->settings->keyBy('name');
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
    // Storage::makeDirectory('public/avatars');
    // $file_name = time().'-'.$id.'.'.$request->avatar->extension();
    // $path = Storage::putFileAs(
    //   'public/avatars', $request->file('avatar'), $file_name
    // );
    // $data['profile_image_url'] = $file_name;
    // $user = Profile::updateOrCreate(
    //   ['user_id' => $id],
    //   $data
    // );
    // return $user;
    if($request->file('avatar')) {
      $file = $request->file('avatar');
      Storage::disk('public_uploads')->makeDirectory('avatars');
      $path = $file->store('avatars', 'public_uploads');
      $profile = Profile::updateOrCreate(
        ['user_id' => $request->user()->id],
        ['profile_image_url' => $path]
      );
      if($profile) {
        $u = User::info()->where('id', $request->user()->id)->first();

        return response()->json($u);
      }

      return response()->json(['status' => 'failed'], 422);
      
    }
  }

  public function getSkills($id) {
    $skills =  User::where('id', $id)->first()->skills;
    return response()->json($skills);
  }
  
  public function getTags($id) {
    $tags =  User::where('id', $id)->first()->tags;
    return response()->json($tags);
  }

  public function addSkill(Request $request, $id) {
    $data = $request->all();
    $user_id = $request->user()->id;
    $user = User::where('id', $user_id)->first();
    if(isset($data['skill'])) {
      $_skill = Skill::where('skill', $data['skill'])->first();
      if( !$_skill ) {
        $skill = Skill::create([
          'skill' => ucwords($data['skill'])
        ]);
        $user->skills()->attach($skill->id);
        return response()->json($skill);
      } else {
        $user->skills()->attach($_skill->id);
        return response()->json($_skill);
      }
    }
    
    $user->skills()->attach($data['id']);
    return response()->json($user->skills);
  }

  public function addTag(Request $request, $id) {
    $user_id = $request->user()->id;
    $user = User::where('id', $user_id)->first();
    if(isset($request->tag_id)) {
      $user->tags()->attach($request->tag_id);
      return response()->json(['message' => 'ok']);
    }
  }
  

  public function removeSkill($id, $skill_id) {
    $user = User::where('id', $id)->first();
    $user->skills()->detach($skill_id);
    return response()->json($user->skills);
  }
  
  public function removeTag($id, $tag_id) {
    $user = User::where('id', $id)->first();
    $user->tags()->detach($tag_id);
    return response()->json($user->tags);
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
      'user_id' => $request->user_id,
      'job_id' => $request->job_id,
      'review' => $request->review,
      'stars' => $request->stars
    ]);

    if($review) {
      $job = Job::infoWithBidders()->where('id', $data['job_id'])->first();
      $job->status = 4;
      $job->is_reviewed = true;
      $job->is_completed = true;
      $job->save();

      Transaction::create([
        'supplier_id' => $job->winner->user->id,
        'customer_id' => $request->user()->id,
        'amount' => $job->winner->bid->price_bid,
        'job_id' => $job->id,
        'bid_id' => $job->winner->bid->id,
        'status' => '1',
        'payment_type' => 'cod'
      ]);

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
      
      $this->sendNotification($notifiable->id, $title, $bid_id);

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

    foreach($data['locations'] as $location) {
      Location::updateOrCreate(
        ['id' => $location['id']],
        $location
      );
    }

    $user = User::info()->where('id', $user_id)->first();
    return response()->json($user);

  }

  public function deleteLocation(Request $request, $id, $location_id) {
    $user_id = $request->user()->id;

    $del = Location::destroy($location_id);

    $user = User::info()->where('id', $user_id)->first();
    return response()->json($user);

  }

  public function markNotificationAsRead(Request $request, $id) {

    $notification = $request->user()->notifications()->where('id',$request->id)->first();
    if($notification) {
      $notification->markAsRead();
      // $o_notifications = $request->user()->unreadNotifications;
      // foreach($o_notifications as $n) {
      //   if($n->job_id == $notification->job_id) {
      //     $n->markAsRead();
      //   }
      // }
      
      $notifications = $request->user()->unreadNotifications->groupBy('data.job_id');
      
      return response()->json($notifications);
    }

    return response()->json(['status' => 'failed'], 422);
  }

  public function getUserByUsername($username) {

    $user = User::info()->where('username', $username)->first();

    return response()->json($user);

  }

  public function resendEmail(Request $request, $id) {
    
    $user = User::where('id', $request->user()->id)->first();

    $data['confirmation_token'] = $user->confirmation_token;
    $data['name'] = $user->name;
    $data['email'] = $user->email;
    
    Mail::send('mails.confirmation', $data, function($message) use($data) {
      $message->to($data['email']);
      $message->subject('Email Verification');
    });

    if(Mail::failures()) {
      return response()->json(['status' => 'failed'], 422);
    }
    
    return response()->json(['status' => 'ok']);
    
  }

  public function updateMobile(Request $request, $id) {
    
    $user = User::info()->where('id', $request->user()->id)->first();

    $data = $request->all();

    $user->phone_number = $data['phone_number'];

    $user->save();
    
    return response()->json($user);
    
  }

  public function updateSetting(Request $request, $id) {
    $user_id = $request->user()->id;

    $data = $request->all();
    $data['user_id'] = $user_id;  

    Setting::updateOrCreate(
      [
        'user_id' => $user_id,
        'name' => $data['name']
      ],
      $data
    );

    $user = User::info()->where('id', $user_id)->first();
    $user->keyedSettings = $user->settings->keyBy('name');
    return response()->json($user);

  }

  public function getActiviyLogs(Request $request, $id) {
    $logs = Activity::where('causer_id', $request->user()->id)->orderBy('created_at', 'DESC')->get();
    return response()->json($logs);
  }

  public function updateBio(Request $request, $id) {
    
    $data = $request->all();

    Profile::updateOrCreate(
      ['user_id' => $request->user()->id],
      ['bio' => $data['bio']]
    );

    $user = User::info()->where('id', $request->user()->id)->first();
    $user->keyedSettings = $user->settings->keyBy('name');
    
    return response()->json($user);
    
  }

  public function updateName(Request $request, $id) {
    
    $data = $request->all();
    $user = User::where('id', $request->user()->id)->first();
    $user->name = $data['name'];
    $user->save();

    Profile::updateOrCreate(
      ['user_id' => $request->user()->id],
      [
        'first_name' => $data['first_name'],
        'last_name' => $data['last_name']
      ]
    );

    $user = User::info()->where('id', $request->user()->id)->first();
    $user->keyedSettings = $user->settings->keyBy('name');
    
    return response()->json($user);
    
  }

  public function getReviews(Request $request, $id) {
    
    $user_id = $id;
    if($request->user()) {
      $user_id = $request->user()->id;
    }

    $reviews = Review::where('user_id', $user_id)
      ->with('job')
      ->orderBy('stars', 'desc')
      ->get();
    
    return response()->json($reviews);
  }

  public function getMyFeedback(Request $request) {
    $feedbacks = Feedback::where('user_id', $request->user()->id)
                      ->orderBy('created_at', 'DESC')            
                      ->get();

    return response()->json($feedbacks);
  }

  public function newFeedback(Request $request) {
    $user_id = 0;
    $data = $request->all();
    if($request->user()) {
      $user_id = $request->user()->id;
    }

    $data['user_id'] = $user_id;

    $feedback = Feedback::create($data);

    if($feedback) {
      return response()->json($feedback);
    }

    return response()->json(['status' => 'failed'], 422);

  }

  public function newTransaction(Request $request, $id) {
    $user_id = $request->user()->id;

    $data = $request->all();

    $transaction = Transaction::create([
      'supplier_id' => $data['supplier_id'],
      'customer_id' => $data['customer_id'],
      'amount' => $data['amount'],
      'job_id' => $data['job_id'],
      'bid_id' => $data['bid_id'],
      'status' => $data['status'],
      'payment_type' => $data['payment_type']
    ]);

    if($transaction) {
      return response()->json($transaction);
    }

    return response()->json(['status' => 'failed'], 422);
  }

  public function getTransactions(Request $request, $id) {
    $user_id = $request->user()->id;
    
    $transactions = Transaction::info()
                    ->where('supplier_id', $user_id)
                    ->orWhere('customer_id', $user_id)
                    ->orderBy('created_at', 'DESC')
                    ->paginate(env('JOBS_PER_PAGE'));

    if($transactions) {
      return response()->json($transactions);
    }

    return response()->json(['status' => 'failed'], 422);
  }

  public function sendNotification($user_id, $message, $bid_id) {

    $user = User::where('id', $user_id)->with('settings')->first();
    
    $settings = $user->settings->keyBy('name');

    if(isset($settings['notifications'])) {
      if($settings['notifications']['value'] != 1) {
        return;
      }
    }
		
		OneSignal::sendNotificationUsingTags(
			$message, 
			array(
				array('key' => 'user_id', 'relation' => '=', 'value' => $user_id,)
			),
			$url = env('APP_URL') . '/bids/' . $bid_id);
	}

}
