<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SubCategory as Tag;
use Cache;

class TagController extends Controller
{
    public function getAllTags(Request $request) {

        $tags = Cache::remember('tags', 15, function() {
		  return Tag::info()->get();
		});
        
        return response()->json($tags);
    }
}
