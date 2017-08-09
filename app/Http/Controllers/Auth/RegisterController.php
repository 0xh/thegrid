<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\SocialProvider;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Mail;
use Twilio\Rest\CLient;
use Cookie;

use Socialite;

class RegisterController extends Controller
{
  /*
  |--------------------------------------------------------------------------
  | Register Controller
  |--------------------------------------------------------------------------
  |
  | This controller handles the registration of new users as well as their
  | validation and creation. By default this controller uses a trait to
  | provide this functionality without requiring any additional code.
  |
  */

  use RegistersUsers;

  /**
  * Where to redirect users after registration.
  *
  * @var string
  */
  protected $redirectTo = '/home';

  /**
  * Create a new controller instance.
  *
  * @return void
  */
  public function __construct()
  {
    $this->middleware('guest');
  }

  public function showRegistrationForm()
  {
    return view('polymer');
  }

  /**
  * Handle a registration request for the application.
  *
  * @param  \Illuminate\Http\Request  $request
  * @return \Illuminate\Http\Response
  */
  public function register(Request $request)
  {
    $input = $request->all();
    // trim phone number
    // if($input['phone_number'] != '') {
    //     $input['phone_number'] = $input['country_code'].preg_replace('/[\-\s]/','',$input['phone_number']);
    // }

    $validator = $this->validator($input);

    if($validator->passes()) {
      $data = $this->create($input)->toArray();

      $data['confirmation_token'] = str_random(40);
      $data['confirmation_code'] = $this->generateCode();
      $user = User::find($data['id']);
      $user->confirmation_token = $data['confirmation_token'];
      $user->confirmation_code = $data['confirmation_code'];
      $user->save();

      // TODO: send sms

      if( env('EMAIL_VERIFICATION') ) {
        Mail::send('mails.confirmation', $data, function($message) use($data) {
          $message->to($data['email']);
          $message->subject('Registration Confirmation');
        });
      }
      // IDEA: Login user after register
      $data['user'] = User::where('id', $user->id)
                      ->withCount('jobs')
                      ->withCount('bids')
                      ->with('profile')
                      ->first();
      $data['access_token'] =  $user->createToken('THE GRID')->accessToken;

      return response()->json($data);

      // if( env('SMS') ) {
      //     $sms = $this->sendSMS($user->phone_number, $data['confirmation_code']);
      // } else {
      //     $sms = true;
      // }
      // if( $sms ) {
      //     // return response()->json(['message' => 'A confirmation code will be send to the mobile number provided', 'user' => $user], 200)->withCookie(Cookie::make('user', $user, 30));
      //     return response()->json(['message' => 'A confirmation code will be sent to your email', 'user' => $user]);
      //     // return response()->json(['message' => 'A confirmation link will be send to the email address provided', 'user' => $user], 200);
      // } else {
      //     return response()->json(['message' => 'Sorry! Something went wrong while sending SMS'], 500);
      // }
    }
    return response()->json($validator->errors(), 422);
  }

  public function submitCode(Request $request) {
    // $userCookie = Cookie::get('user');

    $data = $request->all();
    $user = User::where([
      // ['id', '=', $data['id']],
      ['email', '=', $data['email']],
      ['confirmation_code', '=', $data['confirmation_code']]
      ])->first();
      if( $user ) {
        $user->confirmation_code = '';
        // $user->confirmation_code_confirmed = 1;
        $user->confirmed = 1;
        $user->save();
        $user->redirectUrl = route('login');
        return response()->json($user, 200);
      } else {
        return response()->json(['message' => 'Invalid confirmation code'], 422);
      }
    }

    public function resendCode(Request $request) {
      // $userCookie = Cookie::get('user');
      // $code = $this->generateCode();
      $data = $request->all();
      if( $userCookie ) {

        $user = User::where('id', $data['id'])->first();
        if( $user ) {
          $code = $user->confirmation_code;
          if( env('SMS') ) {
            $sms = $this->sendSMS($user->phone_number, $code);
          } else {
            $sms = true;
          }
        } else {
          $sms = false;
        }

      }
      if( $sms ) {
        return response()->json(['message' => 'Successfully send the code to the number provided.', 'cookie' => $userCookie], 200);
      } else {
        return response()->json(['message' => 'Sorry! Something went wrong while sending SMS'], 500);
      }
    }

    public function generateCode() {
      $length = env('CONFIRMATION_CODE_LENGTH');
      $start = '1';
      $end = '9';
      for($i = 1; $i < $length; $i++) {
        $start .= '0';
        $end .= '9';
      }
      $code = mt_rand($start, $end);
      return $code;
    }

    public function sendSMS($phone_number, $code) {
      $client = new Client(env('TWILIO_ACCOUNT_SID'), env('TWILIO_AUTH_TOKEN'));

      $to = $phone_number;
      $message = sprintf('THE GRID: Your %d digit pin code is %d', env('CONFIRMATION_CODE_LENGTH'), $code);
      $twilioNumber = env('TWILIO_NUMBER');
      try {
        $client->messages->create( $to, ["body" => $message,"from" => $twilioNumber]);
        return true;
      } catch (Exception $e) {
        // var_dump($e);
        return false;
      }
    }

    public function confirmation($token) {
      $user = User::where('confirmation_token', $token)->first();

      if(!is_null($user)) {
        $user->confirmed = 1;
        $user->confirmation_token = '';
        $user->save();
        // return redirect(route('login'))->with('status', 'Email Activated');
        return response()->json(['message' => 'Email has been confirmed!']);
      }
      // return redirect(route('login'))->with('status', 'Something went wrong');
      return response()->json(['message' => 'Something went wrong...'], 500);
    }
    /**
    * Get a validator for an incoming registration request.
    *
    * @param  array  $data
    * @return \Illuminate\Contracts\Validation\Validator
    */
    protected function validator(array $data)
    {
      $unique = "|unique:users";
      if( isset($data['user_id']) ) {
        if($data['user_id']) {
          $unique = "";
        }
      }
      return Validator::make($data, [
        'name' => 'required|string|max:255',
        'username' => 'required|alpha_dash|unique:users',
        'email' => 'required|string|email|max:255'.$unique,
        'phone_number' => 'required|string|unique:users',
        'gender' => 'required',
        'birth_date' => 'required',
        'password' => 'required|string|min:6',
        // 'password' => 'required|string|min:6|confirmed',
      ]);
    }

    /**
    * Create a new user instance after a valid registration.
    *
    * @param  array  $data
    * @return User
    */
    protected function create(array $data)
    {
      // return User::create([
      //   'name' => $data['name'],
      //   'username' => $data['username'],
      //   'email' => $data['email'],
      //   'phone_number' => $data['phone_number'],
      //   'gender' => $data['gender'],
      //   'birth_date' => date("Y-m-d H:i:s", strtotime($data['birth_date'])),
      //   'password' => bcrypt($data['password']),
      // ]);

      // update user if email exist and create if not
      return User::updateOrCreate(
        ['email' => $data['email']],
        ['name' => $data['name'],
        'username' => $data['username'],
        'email' => $data['email'],
        'phone_number' => $data['phone_number'],
        'gender' => $data['gender'],
        'birth_date' => date("Y-m-d H:i:s", strtotime($data['birth_date'])),
        'password' => bcrypt($data['password']),
      ]);
    }

    /**
    * Redirect the user to the Google authentication page.
    *
    * @return Response
    */
    public function redirectToProvider($provider)
    {
      try {
        return Socialite::driver($provider)->redirect();
      } catch(\Exception $e) {
        return redirect('/');
      }
    }

     public function handleProviderCallback($provider)
     {
         try {
             $socialUser = Socialite::driver($provider)->stateless()->user();
             // dd($socialUser);
         } catch(\Exception $e) {
             return redirect(env('APP_URL'));
         }
         $setup_account = false;
         //check if we have logged provider
         $socialProvider = SocialProvider::where('provider_id',$socialUser->getId())->first();

         if(!$socialProvider) {
             //create a new user and provider
             $user = User::firstOrCreate(
               ['email' => $socialUser->getEmail()],
               ['name' => $socialUser->getName()],
               ['confirmed' => 1]
             );
             $user->socialProviders()->create(
               ['provider_id' => $socialUser->getId(), 'provider' => $provider]
             );
             $setup_account = true;
         } else {
             $user = $socialProvider->user;
         }

        //  $token = $socialUser->token;

        //  $qs = http_build_query(array(
        //        'grant_type' => 'social',
        //        'client_id' => env('CLIENT_ID'),
        //        'client_secret' => env('CLIENT_SECRET'),
        //        'network' => $provider,
        //        'access_token' => $token
        //  ));
         //
        //  $client_secret = env('CLIENT_SECRET');
        //  $_setup_account = ($setup_account) ? 1 : 0;

         $data['user'] = $user;
         $data['user']->profile = $user->profile;
         $data['access_token'] =  $user->createToken('THE GRID')->accessToken;

         return response()->json($data);

        //  return redirect(env('APP_URL')."/login/{$provider}/{$token}/{$client_secret}/{$_setup_account}");
         // dd($user);
         // OAuth Two Providers
         // $token = $socialUser->token;
         // $refreshToken = $socialUser->refreshToken; // not always provided
         // $expiresIn = $socialUser->expiresIn;

         // OAuth One Providers
         // $token = $socialUser->token;
         // // // $tokenSecret = $socialUser->tokenSecret;
         // $http = new \GuzzleHttp\Client();
         // $response = $http->post(env('APP_API_URL') . '/oauth/token/', [
         //     'form_params' => [
         //         'grant_type' => 'social',
         //         'client_id' => env('CLIENT_ID'),
         //         'client_secret' => env('CLIENT_SECRET'),
         //         'network' => $provider, /// or any other network that your server is able to resolve.
         //         'access_token' => $token,
         //     ]
         // ]);
         // $response = $http->get('https://127.0.0.1/test');
         // $account = Socialite::driver($provider)->userFromToken($token);

         //dd($account);
         // $response = $http->get('https://api.github.com/repos/guzzle/guzzle');
         // auth()->login($user);
         // return $response;
         // dd($user);
         // if ($request->expectsJson()) {
         //     return response()->json(['user' => $account, 'token' => $response]);
         // }
         //dd($response);
         //exit;
         //return redirect('/');
     }

     public function handleProviderCallbackAPI(Request $request, $provider) {

       $data = $request->all();
       $socialProvider = SocialProvider::where('provider_id', $data['id'])->first();
       $data['new_user'] = 0;
       if(!$socialProvider) {
          //create a new user and provider
          $user = User::firstOrCreate(
            ['email' => $data['email']],
            ['name' => $data['name']],
            ['confirmed' => 1]);

          $user->socialProviders()->create(['provider_id' => $data['id'], 'provider' => $provider]);

          // if the user does not have a user name then he is a new user
          if( !$user->username ) {
            $data['new_user'] = 1;
          }

       } else {
           $user = $socialProvider->user;
           if( !$user->username ) {
             $data['new_user'] = 1;
           }
       }

       $data['user'] = User::where('id', $user->id)
                       ->withCount('jobs')
                       ->withCount('bids')
                       ->with('profile')
                       ->first();
       $data['access_token'] =  $user->createToken('THE GRID')->accessToken;

       return response()->json($data);

     }

     public function validateStep1(Request $request) {
       $input = $request->all();

       $unique = "|unique:users";
       if( isset($input['user_id']) ) {
         if($input['user_id']) {
           $unique = "";
         }
       }

       $validator =  Validator::make($input, [
         'name' => 'required|string|max:255',
         'email' => 'required|string|email|max:255'.$unique,
         'phone_number' => 'required|string|unique:users',
         'password' => 'required|string|min:6',
       ]);

       if( $validator->passes() ) {
         return response()->json(['status' => 'OK']);
       }

       return response()->json($validator->errors(), 422);
     }
}
