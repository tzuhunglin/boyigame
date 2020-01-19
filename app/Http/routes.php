<?php

Route::get('/', function () {
    return view('index');
});
Route::get('Product/Card/Poke/blackjack/{hashkey}/sumup',['as' => 'blackjack.sumup','uses'=>'Product\Card\Poke\BlackjackController@sumup']);

Route::auth();

Route::group(['prefix' => 'Product/Lottery/Threed/jisupailie3'], function () {
	Route::get('/',['as' => 'jisupailie3.index','uses'=>'Product\Lottery\Threed\Jisupailie3Controller@index']);
	Route::post('/bet',['as' => 'jisupailie3.bet','uses'=>'Product\Lottery\Threed\Jisupailie3Controller@bet']);
	Route::get('/awardtest',['as' => 'jisupailie3.awardtest','uses'=>'Product\Lottery\Threed\Jisupailie3Controller@awardtest']);

});

Route::group(['prefix' => 'Product/Card/Poke/blackjack'], function () {
	Route::get('/',['as' => 'blackjack.index','uses'=>'Product\Card\Poke\BlackjackController@index']);
});

Route::group(['prefix' => 'Manage/Children'], function () {
	Route::match(['post','get'],'create',['as' => 'children.create','uses'=>'Manage\ChildrenController@create']);
	Route::get('{id}',['as' => 'children.index','uses'=>'Manage\ChildrenController@index']);
	Route::match(['post','get'],'/{$id}/edit',['as' => 'children.edit','uses'=>'Manage\ChildrenController@edit']);
	Route::get('/{id}/show',['as' => 'children.show','uses'=>'Manage\ChildrenController@show']);
	Route::get('/{id}/betRecord',['as' => 'children.betRecord','uses'=>'Manage\ChildrenController@betRecord']);
	Route::get('/{id}/gameRecord',['as' => 'children.gameRecord','uses'=>'Manage\ChildrenController@gameRecord']);
	Route::get('/{id}/betRecordDetail',['as' => 'children.betRecordDetail','uses'=>'Manage\ChildrenController@betRecordDetail']);
	Route::get('/{id}/gameRecordDetail',['as' => 'children.gameRecordDetail','uses'=>'Manage\ChildrenController@gameRecordDetail']);
	Route::get('/{id}/returnRecord',['as' => 'children.returnRecord','uses'=>'Manage\ChildrenController@returnRecord']);
});

Route::get('/fetchjuisupailie3code', 'CronJisupailie3@handle');

