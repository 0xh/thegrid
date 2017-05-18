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

	// foreach ($users as $user) {
	// 	echo $user->name . ' : ' . $user->items().join(',');
	// }
	return response()->json(request()->user());
})->middleware('auth:api');

Route::get('/user/{id}', function($id) {
	$result = Array();
	$items = App\User::find($id)->items;

	$result = Array("user" => App\User::find($id), "items" => $items);
	return $result;
});

Route::get('/items', function() {
	$items = App\Item::all();

	foreach ($items as $item) {
	//	echo $item->item_name . ' : ' . $item->user->name . '<br/>';
	}
	return $items;
});

Route::get('/grid-elements', 'ElementsController@index');
Route::get('/grid-elements/{element}', 'ElementsController@element');


Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/auth/{provider}', 'Auth\RegisterController@redirectToProvider');
Route::get('/auth/{provider}/callback', 'Auth\RegisterController@handleProviderCallback');
