<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\User;

use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
  /*
  |--------------------------------------------------------------------------
  | Login Controller
  |--------------------------------------------------------------------------
  |
  | This controller handles authenticating users for the application and
  | redirecting them to your home screen. The controller uses a trait
  | to conveniently provide its functionality to your applications.
  |
  */

  use AuthenticatesUsers;

  public function showLoginForm() {
    return view('polymer');
  }


  public function login(Request $request) {

    $this->validateLogin($request);
    // dd($this->attemptLogin($request));
    if ($this->attemptLogin($request)) {
      return $this->sendLoginResponse($request);
    }

    return $this->sendFailedLoginResponse($request);
  }

  public function loginAPI(Request $request) {

    $this->validateLogin($request);
    if ($this->attemptLogin($request)) {
      return $this->sendLoginResponseAPI($request);
    }

    return $this->sendFailedLoginResponseAPI($request);
  }

  protected function sendLoginResponseAPI(Request $request) {

    $data['user'] = User::find(Auth::user()->id)
                    ->withCount('jobs')
                    ->withCount('bids')
                    ->with('profile')
                    ->first();
    $data['access_token'] =  Auth::user()->createToken('THE GRID')->accessToken;

    return response()->json($data);
    // return $response;
  }

  protected function sendFailedLoginResponseAPI(Request $request) {
    return response()->json(['message' => 'Invalid email or password'], 401);
  }

  /**
  * Validate the user login request.
  *
  * @param  \Illuminate\Http\Request  $request
  * @return void
  */
  protected function validateLogin(Request $request)
  {
    $this->validate($request, [
      $this->username() => 'required|email',
      'password' => 'required|string',
    ]);
  }

  /**
  * Send the response after the user was authenticated.
  *
  * @param  \Illuminate\Http\Request  $request
  * @return \Illuminate\Http\Response
  */
  protected function sendLoginResponse(Request $request)
  {
    $request->session()->regenerate();

    $this->clearLoginAttempts($request);
    // $http = new \GuzzleHttp\Client(['defaults' => ['verify' => false]]);
    // $response = $http->post(env('APP_URL').'/oauth/token', [
    //     'form_params' => [
    //         'grant_type' => 'password',
    //         'client_id' => 2,
    //         'client_secret' => 'PxiXYcdKz1bu39THhwdXtgQGrHt7yqvguE8K67Ea',
    //         'username' => $request->email, /// or any other network that your server is able to resolve.
    //         'password' => $request->password
    //     ],
    // ]);


    $response = ['status' => 1];

    return $response;

    //return $this->authenticated($request, $this->guard()->user())
    //       ?: redirect()->intended($this->redirectPath());
  }

  /**
  * Get the needed authorization credentials from the request.
  *
  * @param  \Illuminate\Http\Request  $request
  * @return array
  */
  protected function credentials(Request $request)
  {
    // return $request->only($this->username(), 'password');
    return ['email' => $request->{$this->username()},
    'password' => $request->password];
    // 'confirmed' => 1];
  }

  /**
  * Get the failed login response instance.
  *
  * @param  \Illuminate\Http\Request  $request
  * @return \Illuminate\Http\RedirectResponse
  */
  protected function sendFailedLoginResponse(Request $request)
  {
    $errors = [$this->username() => trans('auth.failed')];

    if ($request->expectsJson()) {
      //return response()->json($errors, 422);
      // $user = User::where($this->username(), $request->{$this->username()})->first();
      //if( !is_null($user) ) {
      //return response()->json(['errors' => $errors, 'message' => 'Please verify your email first.'], 422);
      return response()->json($errors, 422);
      //}
      //return response()->json(['status' => '0'], 200);
    }

    return redirect()->back()
    // return redirect('/login')
    ->withInput($request->only($this->username(), 'remember'))
    ->withErrors($errors);
  }

  /**
  * Get the login username to be used by the controller.
  *
  * @return string
  */
  public function username()
  {
    return 'email';
  }

  /**
  * Where to redirect users after login.
  *
  * @var string
  */
  protected $redirectTo = '/';

  /**
  * Create a new controller instance.
  *
  * @return void
  */
  public function __construct()
  {
    $this->middleware('guest')->except('logout');
  }
}
