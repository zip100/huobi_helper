<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    const MODE_BUY = 1;
    const MODE_SELL = 2;
    const MODE_MARKET_BUY = 3;
    const MODE_MARKET_SELL = 4;
}
