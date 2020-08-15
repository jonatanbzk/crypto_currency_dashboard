<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::resource('/','CoinController');

Route::get('/getCoins','CoinController@getCoins');
Route::POST('/coinData/getCoinData/{id}','CoinController@getCoinData');
