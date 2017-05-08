<?php

namespace App\Events;

use App\Model\Price;
use App\Model\Range;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class RangeNoticeEvent
{
    use Dispatchable, InteractsWithSockets;


    public $range;
    public $price;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Range $range)
    {
        $this->range = $range;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }

    public function bindPrice(Price $price)
    {
        $this->price = $price;
        return $this;
    }
}
