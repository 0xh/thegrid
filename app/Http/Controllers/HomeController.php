<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Job;
use App\User;
use App\Country;
use Location;
use Cache;

class HomeController extends Controller
{
    public function getLocationByIp(Request $request) {
        $ip = $this->get_ip();

        // for dev purposes
        if($ip == '10.0.0.1') {
            $ip = '83.110.226.117';
        }

        $location = Cache::remember('location', 15, function() use($ip) {
            return Location::get($ip);
        });

        if($request->user() && $location) {
            $user = User::where('id', $request->user()->id)->first();
            $country = Country::where('iso', $location->countryCode)->first();
            $user->country_id = $country->id;
            $user->save();
        }

        return response()->json($location);
    }

    public function getCountryDetails(Request $request) {
        $ip = $this->get_ip();
        
        // for dev purposes
        if($ip == '10.0.0.1') {
            $ip = '83.110.226.117';
        }

        $location = Cache::remember('location', 15, function() use($ip) {
            return Location::get($ip);
        });

        $country = Country::where('iso', $location->countryCode)->first();

        return response()->json($country);
    }
    
    public function getCountries(Request $request) {
        
        $countries = Country::where('currency_unit', '!=', null)
            ->get();

        return response()->json($countries);
    }
    
    public function getPhoneCodes(Request $request) {
        
        $countries = Country::where('phonecode', '!=', 0)
            ->get();

        return response()->json($countries);
    }

    public function getOG($id) {
        $job = Job::where('id', $id)->first();
        
        if($job) {
    
            $image = 'https://maps.googleapis.com/maps/api/staticmap?center='.$job->lat.','.$job->lng.'&zoom=10&format=png&maptype=roadmap&style=element:labels|visibility:on&style=element:labels.icon|visibility:off&style=element:labels.text.fill|color:0x000000|saturation:36|lightness:40&style=element:labels.text.stroke|color:0x000000|lightness:16|visibility:on&style=feature:administrative|element:geometry.fill|color:0x000000|lightness:20&style=feature:administrative|element:geometry.stroke|color:0x000000|lightness:17|weight:1.2&style=feature:administrative.country|element:labels.text.fill|color:0xFFFFFF&style=feature:administrative.locality|element:labels.text.fill|color:0xc4c4c4&style=feature:administrative.neighborhood|element:labels.text.fill|color:0xffffff&style=feature:landscape|element:geometry|color:0x000000|lightness:20&style=feature:poi|element:geometry|color:0x000000|lightness:21|visibility:on&style=feature:poi.business|element:geometry|visibility:on&style=feature:road.arterial|element:geometry|color:0x000000|lightness:18&style=feature:road.arterial|element:geometry.fill|color:0x575757&style=feature:road.arterial|element:labels.text.fill|color:0xffffff&style=feature:road.arterial|element:labels.text.stroke|color:0x2c2c2c&style=feature:road.highway|element:geometry.fill|color:0x575757|lightness:0&style=feature:road.highway|element:geometry.stroke|visibility:off&style=feature:road.highway|element:labels.text.fill|color:0xffffff&style=feature:road.highway|element:labels.text.stroke|color:0x000000&style=feature:road.local|element:geometry|color:0x000000|lightness:16&style=feature:road.local|element:labels.text.fill|color:0x999999&style=feature:transit|element:geometry|color:0x000000|lightness:19&style=feature:water|element:geometry|color:0x000000|lightness:17&size=600x314&markers=icon:https://thegridpolymer.azurewebsites.net/assets/images/icons/related-marker.png|'.$job->lat.','.$job->lng.'&key=AIzaSyC84X-WOY2nTHBYWhXZdb1sAGZbvlXxvrw';
        
            $og = [
                'title' => $job->name,
                'description' => $job->details,
                'image' => $image
            ];
            return response()->json($og);
        }
    
        return response()->json(['error' => 'invalid url']);
    }

    public function getAppVersion() {
        return response()->json(['version' => env('APP_VERSION')]);
    }

    public function get_ip() {
		//Just get the headers if we can or else use the SERVER global
		if ( function_exists( 'apache_request_headers' ) ) {
			$headers = apache_request_headers();
		} else {
			$headers = $_SERVER;
		}
		//Get the forwarded IP if it exists
		if ( array_key_exists( 'X-Forwarded-For', $headers ) && filter_var( $headers['X-Forwarded-For'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
			$the_ip = $headers['X-Forwarded-For'];
		} elseif ( array_key_exists( 'HTTP_X_FORWARDED_FOR', $headers ) && filter_var( $headers['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 )
		) {
			$the_ip = $headers['HTTP_X_FORWARDED_FOR'];
		} else {
			
			$the_ip = filter_var( $_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 );
		}
		return $the_ip;
	}

}
