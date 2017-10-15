<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Job;
use Location;

class HomeController extends Controller
{
    public function getLocationByIp(Request $request) {
        $ip = $request->ip();

        // for dev purposes
        if($ip == '10.0.0.1') {
            $ip = '83.110.226.117';
        }

        $location = Location::get($ip);

        return response()->json($location);
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
}
