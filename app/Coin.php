<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coin extends Model
{
    protected $fillable = ['name', 'abbreviation'];

    /**
     * Get the prices for the coin.
     */
    public function prices()
    {
        return $this->hasMany('App\Price');
    }
}
