<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Twilio\Rest\CLient;

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

    public function compress( $minify )
    {
  /* remove comments */
      $minify = preg_replace( '!/*[^*]**+([^/][^*]**+)*/!', '', $minify );

        /* remove tabs, spaces, newlines, etc. */
      $minify = str_replace( array("rn", "r", "n", "t", '  ', '    ', '    '), '', $minify );

        return $minify;
    }

    public function min(Request $request) {
      header('Content-type: text/html');
      ob_start();


          /* html files for combining */
          // include(public_path().'/bower_components/polymer/polymer.html');
          include(public_path().'/bower_components/iron-flex-layout/iron-flex-layout-classes.html');
          include(public_path().'/bower_components/paper-material/paper-material.html');
          include(public_path().'/bower_components/paper-ripple/paper-ripple.html');
          include(public_path().'/bower_components/paper-scroll-header-panel/paper-scroll-header-panel.html');
          include(public_path().'/bower_components/paper-header-panel/paper-header-panel.html');
          include(public_path().'/bower_components/paper-toolbar/paper-toolbar.html');
          include(public_path().'/bower_components/paper-button/paper-button.html');
          include(public_path().'/bower_components/paper-fab/paper-fab.html');
          include(public_path().'/bower_components/iron-icons/social-icons.html');
          include(public_path().'/bower_components/iron-icons/communication-icons.html');
          include(public_path().'/bower_components/iron-icons/maps-icons.html');
          include(public_path().'/bower_components/iron-icons/hardware-icons.html');
          include(public_path().'/bower_components/paper-icon-button/paper-icon-button.html');
          include(public_path().'/bower_components/iron-media-query/iron-media-query.html');
          include(public_path().'/bower_components/paper-menu/paper-menu.html');
          include(public_path().'/bower_components/paper-item/paper-item.html');
          include(public_path().'/bower_components/paper-item/paper-icon-item.html');
          include(public_path().'/bower_components/paper-progress/paper-progress.html');
          include(public_path().'/bower_components/paper-dialog/paper-dialog.html');
          include(public_path().'/bower_components/neon-animation/animations/scale-down-animation.html');
          include(public_path().'/bower_components/gold-email-input/gold-email-input.html');
          include(public_path().'/bower_components/gold-phone-input/gold-phone-input.html');


      ob_end_flush();
    }
}
