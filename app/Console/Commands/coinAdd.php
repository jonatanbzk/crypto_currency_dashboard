<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Coin;
class coinAdd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'coin:add {coin_name*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add new coin to database. Needs coin name &  coin
    abbreviation as parameter. ex: coin:add bitcoin btc';

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
        $arguments = $this->argument('coin_name');
        if (count($arguments) !== 2) {
            return $this->error('coin:add command needs coin name & coin abbreviation as parameter. ex: coin:add bitcoin btc');
        }
        $count = Coin::where('name', $arguments[0])->count();
        if ($count > 0) {
            return $this->error($arguments[0] . ' is already in the database.');
        }
        DB::insert('insert into coins (name, abbreviation) values (?, ?)',
            [$arguments[0], $arguments[1]]);

        $this->info($arguments[0] . ' have been added to the database.');
    }
}
