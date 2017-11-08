<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SubCategory as Tag;

class TagController extends Controller
{
    public function getAllTags(Request $request) {
        $tags = Tag::info()
            ->get();
        
        return response()->json($tags);
    }
}
