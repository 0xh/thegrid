<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use App\Transformers\Json;
use App\User;
use Mail;
use Twilio\Rest\CLient;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showLinkRequestForm()
    {
        return view('polymer');
    }

    public function sendResetLink(Request $request)
    {
      $data = $request->all();

      // $this->validate($request, [
      //     'email' => 'required|email',
      // ]);


    }

    public function getResetToken(Request $request)
    {
        $this->validate($request, ['email' => 'required|email']);
        if ($request->wantsJson()) {
            $user = User::where('email', $request->input('email'))->first();
            if (!$user) {
                return response()->json(Json::response(null, trans('passwords.user')), 400);
            }
            $token = $this->broker()->createToken($user);

            $data['user'] = $user;
            $data['token'] = $token;
            $send = $this->sendPasswordResetLinkByEmail($data);

            return response()->json($send);
            //return response()->json(Json::response(['token' => $token]));
        }
    }

    public function sendPasswordResetLinkByEmail($data) {
      Mail::send('mails.resetlink', $data, function($message) use($data) {
          $message->to($data['user']['email']);
          $message->subject('Request for Password Reset');
      });

      if (Mail::failures()) {
         return ['error' => 'Something went wrong'];
      }

      return ['message' => 'Reset link has been sent to email'];

    }

    

}
