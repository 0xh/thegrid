<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\JobCategory;

class JobCategoryController extends Controller
{
    public function getCategories() {
      return JobCategory::all();
    }
}
