<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Twilio\Rest\Client;
use App\User;
use App\Profile;

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
    	return User::where('id', $id)->first();
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
      $skills =  User::find($id)->skills;
      return response()->json($skills);
    }

    public function addSkill(Request $request, $id) {
      $data = $request->all();
      $user = User::find($id);
      $user->skills()->attach($data['id']);
      return $user->skills;
    }

    public function removeSkill($id, $skill_id) {
      $user = User::find($id);
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
}
