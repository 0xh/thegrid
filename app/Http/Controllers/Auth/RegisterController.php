<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\SocialProvider;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

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
            $socialUser = Socialite::driver($provider)->user();
            // dd($socialUser);
        } catch(\Exception $e) {
            return redirect('/');
        }
        //check if we have logged provider
        $socialProvider = SocialProvider::where('provider_id',$socialUser->getId())->first();
        
        if(!$socialProvider) {
            //create a new user and provider
            $user = User::firstOrCreate(
                ['email' => $socialUser->getEmail()],
                ['name' => $socialUser->getName()]
            );
            $user->socialProviders()->create(
                ['provider_id' => $socialUser->getId(), 'provider' => $provider]
            );
        }
        else {
            $user = $socialProvider->user;
        }

        // OAuth Two Providers
        // $token = $socialUser->token;
        // $refreshToken = $socialUser->refreshToken; // not always provided
        // $expiresIn = $socialUser->expiresIn;

        // // OAuth One Providers
        // $token = $socialUser->token;
        // // $tokenSecret = $socialUser->tokenSecret;
        // $http = new \GuzzleHttp\Client(['defaults' => ['verify' => false]]);
        // $response = $http->post(env('APP_URL').'/oauth/token', [
        //     'form_params' => [
        //         'grant_type' => 'social',
        //         'client_id' => '2',
        //         'client_secret' => 'PxiXYcdKz1bu39THhwdXtgQGrHt7yqvguE8K67Ea',
        //         'network' => 'google', /// or any other network that your server is able to resolve.
        //         'access_token' => $token,
        //     ],
        // ]);
        // //$response = $http->request('GET', 'https://api.github.com/repos/guzzle/guzzle');
        // return $response;
        // dd($user);
        auth()->login($user);
        return redirect('/');
    }
}
