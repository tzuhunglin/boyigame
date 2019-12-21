<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::auth();

// Route::get('/home', 'HomeController@index');



Route::get('/', function () {
    return view('index');
});

Route::auth();

Route::group(['prefix' => 'Product/Lottery/Threed/jisupailie3'], function () {
	Route::get('/',['as' => 'jisupailie3.index','uses'=>'Product\Lottery\Threed\Jisupailie3Controller@index']);
	Route::post('/bet',['as' => 'jisupailie3.bet','uses'=>'Product\Lottery\Threed\Jisupailie3Controller@bet']);
});
Route::group(['prefix' => 'Manage/Children'], function () {
	Route::match(['post','get'],'create',['as' => 'children.create','uses'=>'Manage\ChildrenController@create']);

	Route::get('{id}',['as' => 'children.index','uses'=>'Manage\ChildrenController@index']);




	// Route::get('/{id}/edit',['as' => 'children.edit','uses'=>'Manage\ChildrenController@edit']);
	Route::match(['post','get'],'/{$id}/edit',['as' => 'children.edit','uses'=>'Manage\ChildrenController@edit']);

	Route::get('/{id}/show',['as' => 'children.show','uses'=>'Manage\ChildrenController@show']);
	Route::get('/{id}/betRecord',['as' => 'children.betRecord','uses'=>'Manage\ChildrenController@betRecord']);

});


// Route::prefix('Manage/Children')->group(function(){
// 	Route::match(['post','get'],'create',['as' => 'children.create','uses'=>'Manage\ChildrenController@create']);

// 	Route::get('{id}',['as' => 'children.index','uses'=>'Manage\ChildrenController@index']);




// 	// Route::get('/{id}/edit',['as' => 'children.edit','uses'=>'Manage\ChildrenController@edit']);
// 	Route::match(['post','get'],'/{$id}/edit',['as' => 'children.edit','uses'=>'Manage\ChildrenController@edit']);

// 	Route::get('/{id}/show',['as' => 'children.show','uses'=>'Manage\ChildrenController@show']);
// 	Route::get('/{id}/betRecord',['as' => 'children.betRecord','uses'=>'Manage\ChildrenController@betRecord']);

// });

// Auth::routes();

// Route::get('/home', 'HomeController@index')->name('home');
// Route::get('/fetchjuisupailie3code', 'JobController@processQueue');

