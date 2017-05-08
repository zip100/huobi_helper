<?php

namespace App\Events;

use App\Model\Action;
use App\Model\PriceNotice;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ActionEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $priceNotice;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(PriceNotice $priceNotice)
    {
        $this->priceNotice = $priceNotice;
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
}
