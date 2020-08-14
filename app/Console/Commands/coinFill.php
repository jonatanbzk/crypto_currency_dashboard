<?php

namespace App\Console\Commands;

use App\Coin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class coinFill extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'coin:fill {coin_name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fill new coins with prices history. Needs coin name as a parameter';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // get coin name and check if exist
        $coin_name = $this->argument('coin_name');
        $coin = Coin::where('name', $coin_name)->get();
        if (count($coin) === 0) {
            return $this->error($coin_name . ' does not exist in the database.');
        }

        $coin_id = $coin[0]['id'];


        $refTime = time() - (591 * 24 * 3600);
        while ($refTime < time() - 3600) {
            for ($i = 0; $i < 100; $i++) {
                if ($refTime < time() - 3600) {
                    $coinPriceHistory = [];
                    $dateAPI = date('d-m-Y', $refTime);
                    $dateSQL = date('Y-m-d H:i:s', $refTime);
                    while (!isset($coinPriceHistory[0]['price']) ||
                        $coinPriceHistory[0]['price'] === 0) {
                        $response = Http::get('https://api.coingecko.com/api/v3/coins/'
                            . $coin_name . '/history?date=' . $dateAPI . '&localization=false');
                        $rep = $response->json();
                        $coinPriceHistory[] = ['date' => $dateSQL,
                            'price' => round($rep['market_data']['current_price']['eur'], 2)];
                    }
                    DB::insert('insert into prices (coin_id, price, date) values (?, ?, ?)',
                        [$coin_id, $coinPriceHistory[0]['price'], $coinPriceHistory[0]['date']]);
                    $refTime = $refTime + (24 * 3600);
                }
            }
            sleep(65);
        }

        $this->info($coin_name . ' price history database is fill.');
    }
}
