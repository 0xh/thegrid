<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Twilio\Rest\CLient;

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
}
