<?php

namespace App\Http\Controllers;

use App\Coin;
use App\Price;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class CoinController extends Controller
{

    public function index()
    {
        $coinsName = DB::select('select id, coin_name, abbreviation from coins');
        $coinsPrice = [];
        foreach ($coinsName as $coin) {
            $response = Http::get('https://api.coingecko.com/api/v3/simple/price?ids='.$coin->coin_name.'&vs_currencies=eur&include_24hr_change=true');
            $rep = $response->json();
            $coinsPrice[] = [round($rep[$coin->coin_name]['eur'], 2),
                round($rep[$coin->coin_name]['eur_24h_change'], 2)];
        }
        $bitcoin = Coin::with('prices')->where('coin_name', 'bitcoin')->get();
        return view('home')->with(compact('coinsName', 'coinsPrice', 'bitcoin'));

    }

    public function getCoinData($id = 0){
        $coinData = Coin::with('prices')->where('id', $id)->get();
        return json_encode(array('data'=>$coinData));
    }
}
