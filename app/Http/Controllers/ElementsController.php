<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use View;
use Response;

class ElementsController extends Controller
{
    public function index() {
    	$contents = View::make('element');
		$response = Response::make($contents, 200);
		$response->header('Content-Type', 'text/html');
		return $response;
    }
}
