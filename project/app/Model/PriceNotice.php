<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PriceNotice extends Model
{
    const OPERATOR_LT = 1;
    const OPERATOR_GT = 2;

    const STATUS_WAIT = 0;
    const STATUS_FIRE = 1;

    public function fire(Price $price)
    {
        $this->price_id = $price->id;
        $this->status = self::STATUS_FIRE;
        $this->save();
    }

    public function price()
    {
        return $this->hasOne('App\Model\Price', 'id', 'price_id');
    }


    public function action()
    {
        return $this->hasOne('App\Model\Action', 'id', 'action_id');
    }
}
