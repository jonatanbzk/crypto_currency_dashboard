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

        $response = Http::get('https://api.coingecko.com/api/v3/simple/price?ids=grin&vs_currencies=eur');
        $rep = $response->json();
        $coins = Coin::all();
        $prices = Coin::find(1)->prices;

        return view('home')->with(compact('coins','rep', 'prices'));
    }

}
