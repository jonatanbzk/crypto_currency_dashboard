<?php

namespace App\Console;

use App\Coin;
use App\Price;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //fill price history in db
        $schedule->call(function () {
            $coinsList = DB::select('select id, coin_name, history_fill from coins');
            foreach ($coinsList as $coin) {
                if ($coin->history_fill === 0) {
                   $refTime = 1577880000;  // 01/01/2020  12:00
                    while ($refTime < time() - 3600) {
                        for ($i = 0; $i < 100; $i++) {
                            if ($refTime < time() - 3600) {
                                $coinPriceHistory = [];
                                $dateAPI = date('d-m-Y', $refTime);
                                $dateSQL = date('Y-m-d H:i:s', $refTime);
                                    $response = Http::get('https://api.coingecko.com/api/v3/coins/'
                                        . $coin->coin_name . '/history?date=' .
                                        $dateAPI . '&localization=false');
                                    $rep = $response->json();
                                    $coinPriceHistory[] = ['date_at' =>
                                        $dateSQL,
                                        'price' => round($rep['market_data']['current_price']['eur'], 2)];
                                    DB::insert('insert into prices (coin_id, price, date_at, created_at, updated_at) values (?, ?, ?, ?, ?)',
                                    [$coin->id, $coinPriceHistory[0]['price'],
                                        $coinPriceHistory[0]['date_at'],
                                        $dateSQL, $dateSQL]);
                                $refTime = $refTime + (24 * 3600);
                            }
                        }
                        sleep(65);
                    }
                    $updateHistoryFillDB = DB::table('coins')
                        ->where('id', $coin->id)
                        ->update(['history_fill' => 1]);
                }
            }
        })->everyMinute();

        // check API error price == 0
        $schedule->call(function () {
            $coinPriceZero = Price::where('price', 0)->get();
            $count = count($coinPriceZero);
            $i = 0;
            while ($count !== 0) {
                $coin = Coin::where('id', $coinPriceZero[$i]->coin_id)->get();
                $coinName = $coin[0]->coin_name;
                $dateErrorSQL = strtotime($coinPriceZero[$i]->date_at);
                $dateErrorAPI = date( 'd-m-Y', $dateErrorSQL );
                $response = Http::get('https://api.coingecko.com/api/v3/coins/'
                    . $coinName . '/history?date=' .
                    $dateErrorAPI . '&localization=false');
                $rep = $response->json();
                $newPrice = round($rep['market_data']['current_price']['eur'], 2);;
                $update = DB::table('prices')
                    ->where('id', $coinPriceZero[$i]->id)
                    ->update(['price' => $newPrice]);
                $count--;
                $i++;
            }
        })->everyFiveMinutes();

        // get daily price
        $schedule->call(function () {
            $coinsList = DB::select('select id, coin_name from coins');
            foreach ($coinsList as $coin) {
                $coinId = $coin->id;
                $coinPriceLast = Price::where('coin_id', $coinId)->latest()->get();
                $lastDateSQL = strtotime($coinPriceLast[0]->date_at);

               if ($lastDateSQL < time() - 12 * 3600) {
                   $currentTime = time();
                   $dateSQL = date('Y-m-d H:i:s', $currentTime);
                   $response = Http::get('https://api.coingecko.com/api/v3/simple/price?ids='.$coin->coin_name.'&vs_currencies=eur');
                   $rep = $response->json();
                   DB::insert('insert into prices (coin_id, price, date_at, created_at, updated_at) values (?, ?, ?, ?, ?)',
                       [$coin->id, round($rep[$coin->coin_name]['eur'], 2),
                           $dateSQL, $dateSQL, $dateSQL]);
                }
            }
        })->dailyAt('12:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
