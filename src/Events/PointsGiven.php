<?php

namespace Miracuthbert\Royalty\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Miracuthbert\Royalty\Models\Point;

class PointsGiven implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The User instance.
     *
     * @var $user
     */
    public $user;

    /**
     * The Point instance.
     *
     * @var \Miracuthbert\Royalty\Models\Point
     */
    public $point;

    /**
     * Create a new event instance.
     *
     * @param $user
     * @param \Miracuthbert\Royalty\Models\Point $point
     * @return void
     */
    public function __construct($user, Point $point)
    {
        $this->user = $user;
        $this->point = $point;
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return config('royalty.broadcast.name');
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'point' => $this->point,
            'user_points' => [
                'number' => $this->user->points()->number(),
                'shorthand' => $this->user->points()->shorthand(),
            ],
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel(config('royalty.broadcast.channel') . $this->user->id);
    }
}
