<?php

namespace App\Listeners;

use App\Events\ActionEvent;
use App\Events\LastPriceEvent;
use App\Events\PriceNoticeEvent;
use App\Events\RangeNoticeEvent;
use App\Model\Action;
use App\Model\Price;
use App\Model\PriceNotice;
use App\Model\Range;
use App\Module\Huobi;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class HuobiListener
{

    private $actionMap = [
        Action::MODE_BUY => [Huobi::class, 'buy'],
        Action::MODE_MARKET_BUY => [Huobi::class, 'marketBuy'],
        Action::MODE_SELL => [Huobi::class, 'sell'],
        Action::MODE_MARKET_SELL => [Huobi::class, 'marketSell'],
    ];

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  LastPriceEvent $event
     * @return void
     */
    public function handle($event)
    {
    }


    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    public function subscribe($events)
    {
        $events->listen(
            'App\Events\LastPriceEvent',
            'App\Listeners\HuobiListener@lastPrice'
        );
        $events->listen(
            'App\Events\RangeNoticeEvent',
            'App\Listeners\HuobiListener@rangeNotice'
        );
        $events->listen(
            'App\Events\PriceNoticeEvent',
            'App\Listeners\HuobiListener@priceNotice'
        );
        $events->listen(
            'App\Events\ActionEvent',
            'App\Listeners\HuobiListener@actionEvent'
        );
    }

    public function lastPrice($event)
    {

        $price = $event->price;
        $rangeList = Range::get();

        $now = time();

        foreach ($rangeList as $row) {
            // Price Range Match Check Up
            if ($row->range > 0 && Price::where('created_at', '>', $now - $row->offset)->sum('range') > $row->range) {
                // Trigger PriceNotice Event
                event((new RangeNoticeEvent($row))->bindPrice($price));
            }

            // Price Range Match Check Down
            if ($row->range < 0 && Price::where('created_at', '>', $now - $row->offset)->sum('range') < $row->range) {
                // Trigger PriceNotice Event
                event((new RangeNoticeEvent($row))->bindPrice($price));
            }
        }

        $noticeList = PriceNotice::where('type', $price->type)->where('status', PriceNotice::STATUS_WAIT)->get();


        foreach ($noticeList as $row) {
            // Price Gt Check
            if ($row->operator == PriceNotice::OPERATOR_GT && $price->price > $row->preset) {
                $row->fire($price);
                // Trigger PriceNotice Event
                event(new PriceNoticeEvent($row));
            }
            // Price Lt Check
            if ($row->operator == PriceNotice::OPERATOR_LT && $price->price < $row->preset) {
                $row->fire($price);
                // Trigger PriceNotice Event
                event(new PriceNoticeEvent($row));
            }

        }

    }

    public function rangeNotice($event)
    {
        $range = $event->range;
        \Log::info(sprintf('[Event][Range:%s][Price:%s]', $range->id, $event->price->id));
    }

    public function priceNotice($event)
    {
        $priceNotice = $event->priceNotice;

        // Has Relation Action
        if ($priceNotice->action) {
            // Trigger ActionEvent
            event(new ActionEvent($priceNotice));
            \Log::info(sprintf('[Event][PriceNotice:%s][Price:%s][Action:%s]', $priceNotice->id, $priceNotice->price->id, $priceNotice->action->id));

        } else {
            \Log::info(sprintf('[Event][PriceNotice:%s][Price:%s]', $priceNotice->id, $priceNotice->price->id));
        }
    }

    public function actionEvent($event)
    {
        $priceNotice = $event->priceNotice;
        \Log::info(sprintf('[Event][ActionEvent:%s]', $priceNotice->action->id));

        call_user_func($this->actionMap[$priceNotice->action->mode]);
    }
}
