<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\JobCategory;
use Cache;

class JobCategoryController extends Controller
{
    public function getCategories() {
      $categories = Cache::remember('categories', 15, function() {
        return JobCategory::all();
      });

      return response()->json($categories);
    }
}
