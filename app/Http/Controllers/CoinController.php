<?php

namespace App\Http\Controllers;

use App\Coin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class CoinController extends Controller
{

    public function index()
    {

        $coins = Coin::all();
     //   $prices = Coin::find(1)->prices;

        return view('home')->with(compact('coins'));  // , 'prices'
    }

}
