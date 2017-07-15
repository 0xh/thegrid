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
        if($input['phone_number'] != '') {
            $input['phone_number'] = $input['country_code'].preg_replace('/[\-\s]/','',$input['phone_number']);
        }

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

            Mail::send('mails.confirmation', $data, function($message) use($data) {
                $message->to($data['email']);
                $message->subject('Registration Confirmation');
            });
            if( env('SMS') ) {
                $sms = $this->sendSMS($user->phone_number, $data['confirmation_code']);
            } else {
                $sms = true;
            }
            if( $sms ) {
                return response()->json(['message' => 'A confirmation code will be send to the mobile number provided', 'user' => $user], 200)->withCookie(Cookie::make('user', $user, 30));
            } else {
                return response()->json(['message' => 'Sorry! Something went wrong while sending SMS'], 500);
            }
        }
        return response()->json($validator->errors(), 422);
    }

    public function submitCode(Request $request) {
        $userCookie = Cookie::get('user');
        $input = $request->all();
        $user = User::where([
            ['id', '=', $userCookie['id']],
            ['confirmation_code', '=', $input['confirmation_code']]
            ])->first();
        if( $user ) {
            $user->confirmation_code = '';
            $user->confirmation_code_confirmed = 1;
            $user->save();
            $user->redirectUrl = route('login');
            return response()->json($user, 200);
        } else {
            return response()->json(['message' => 'Invalid confirmation code'], 422);
        }
    }

    public function resendCode() {
        $userCookie = Cookie::get('user');
        $code = $this->generateCode();
        if( $userCookie ) {

            $user = User::where('id', $userCookie['id'])->first();
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
            $client->messages->create(
                $to,
                [
                "body" => $message,
                "from" => $twilioNumber
                ]
                );
            return true;
        } catch (Exception $e) {
            var_dump($e);
            return false;
        }
    }

    public function confirmation($token) {
        $user = User::where('confirmation_token', $token)->first();

        if(!is_null($user)) {
            $user->confirmed = 1;
            $user->confirmation_token = '';
            $user->save();

            return redirect(route('login'))->with('status', 'Email Activated');
        }
        return redirect(route('login'))->with('status', 'Something went wrong');
    }
    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone_number' => 'required|string|unique:users',
            'password' => 'required|string|min:6|confirmed',
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
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
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

    /**
     * Obtain the user information from Google.
     *
     * @return Response
     */
    public function handleProviderCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
            // dd($socialUser);
        } catch(\Exception $e) {
            return redirect(env('APP_URL'));
        }
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
        } else {
            $user = $socialProvider->user;
        }

        $token = $socialUser->token;

        $qs = http_build_query(array(
              'grant_type' => 'social',
              'client_id' => env('CLIENT_ID'),
              'client_secret' => env('CLIENT_SECRET'),
              'network' => $provider,
              'access_token' => $token
        ));
        return redirect(env('APP_URL').'?'.$qs);
        // dd($user);
        // OAuth Two Providers
        // $token = $socialUser->token;
        // $refreshToken = $socialUser->refreshToken; // not always provided
        // $expiresIn = $socialUser->expiresIn;

        // OAuth One Providers
        // $token = $socialUser->token;
        // // // $tokenSecret = $socialUser->tokenSecret;
        // $http = new \GuzzleHttp\Client(['defaults' => ['verify' => false]]);
        // $response = $http->post(env('APP_API_URL').'/oauth/token', [
        //     'form_params' => [
        //         'grant_type' => 'social',
        //         'client_id' => env('CLIENT_ID'),
        //         'client_secret' => env('CLIENT_SECRET'),
        //         'network' => $provider, /// or any other network that your server is able to resolve.
        //         'access_token' => $token,
        //     ],
        // ]);
        // $account = Socialite::driver($provider)->userFromToken($token);

        //dd($account);
        // //$response = $http->request('GET', 'https://api.github.com/repos/guzzle/guzzle');
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
}
