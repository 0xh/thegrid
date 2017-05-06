<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('polymer');
});


Route::get('/users', function() {
	$users = App\User::all();

	foreach ($users as $user) {
		echo $user->name . ' : ' . $user->items().join(',');
	}
	//return $users;
});

Route::get('/items', function() {
	$items = App\Item::all();

	foreach ($items as $item) {
		echo $item->item_name . ' : ' . $item->user->name . '<br/>';
	}
	//return $items;
});