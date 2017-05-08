<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 2017/5/8
 * Time: 上午11:06
 */
namespace App\Module;

class Huobi
{
    const BTC = 1;
    CONST LTC = 2;

    public static function getLastPrice($type)
    {
        return rand(1, 100);
    }

    public static function buy($type, Acto)
    {
    }

    public static function marketBuy($type, $amount)
    {
    }

    public static function sell($type, $price, $count)
    {
    }

    public static function marketSell($type, $count)
    {
    }
}