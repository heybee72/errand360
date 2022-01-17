<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(
	[

		'middleware' => 'api',
		'namespace' => 'App\Http\Controllers',
		'prefix'=>'customer'
	], 
	function($router){
		Route::post('login', 	'CustomerController@login');
		Route::post('register', 	'CustomerController@register');
		Route::post('logout', 	'CustomerController@logout');

		Route::get('profile', 	'CustomerController@profile');
		Route::post('changePassword', 'CustomerController@changePassword')->middleware('customer-api');

	}
);



Route::group(
	[

		'middleware' => 'api',
		'namespace' => 'App\Http\Controllers',
		'prefix'=>'rider'
	], 
	function($router){
		Route::post('login', 	'RiderController@login');
		Route::post('register', 	'RiderController@register');
		Route::post('logout', 	'RiderController@logout');

		Route::get('profile', 	'RiderController@profile');
		

		Route::post('changePassword', 'RiderController@changePassword')->middleware('rider-api');

	}
);

Route::group(
	[

		'middleware' => 'api',
		'namespace' => 'App\Http\Controllers',
		'prefix'=>'order'
	], 
	function($router){
		Route::post('createNewOrder', 	'OrderController@createNewOrder')->middleware('customer-api');
		Route::post('addDriverToOrder', 	'OrderController@addDriverToOrder')->middleware('rider-api');
		Route::post('driverRejectRide', 	'OrderController@driverRejectRide')->middleware('rider-api');
		Route::post('riderDeliverOrder', 	'OrderController@riderDeliverOrder')->middleware('rider-api');


	}
);


Route::group(
	[

		'middleware' => 'api',
		'namespace' => 'App\Http\Controllers',
		'prefix'=>'cancelled_ride'
	], 
	function($router){
		Route::get('fetch_cancelled_ride', 	'CancelledRideController@index')->middleware('admin-api');
		Route::delete('delete/{id}', 	'CancelledRideController@delete')->middleware('admin-api');

	}
);


Route::group(
	[

		'middleware' => 'api',
		'namespace' => 'App\Http\Controllers',
		'prefix'=>'admin'
	], 
	function($router){
		Route::post('login', 	'AdminAuthController@login');
		Route::post('register', 	'AdminAuthController@register');
		Route::get('profile', 	'AdminAuthController@profile')->middleware('admin-api');
		Route::post('logout', 	'AdminAuthController@logout');
		
	}
);



