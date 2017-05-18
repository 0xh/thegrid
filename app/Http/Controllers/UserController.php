<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class UserController extends Controller
{
    public function getUserDetails($id) {
    	return response()->json(request()->user());
    }
}
