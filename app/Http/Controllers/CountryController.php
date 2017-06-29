<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Country;

class CountryController extends Controller
{
    public function getCountryByISO($iso) {
    	$country = Country::where('iso', $iso)->first();
    	if( $country ) {
    		return response()->json($country, 200);
    	} else {
    		return response()->json(['error' => 'true'], 200);
    	}
    }
}
