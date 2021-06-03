<?php

use Illuminate\Support\Facades\Route;

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
    return view('welcome');
});


Route::get('query', function () {
    return view('query');
});

Route::get('orders', 'OrderController@index');

// Route::get('orders', function(){

//     $customer_id= rand(1,29);
//     echo $customer_id . " | ";
//     $query_customerbuild = DB::table('customers')
//         ->where('id', '=', ''.$customer_id.'')
//         ->get();
//     return view('tcc.index', ['build' => $query_customerbuild]);

// });