<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendHandShake implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $data, $senderId, $reciverId, $caller;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($senderId, $reciverId, $data)
    {
        $this->senderId = $senderId;
        $this->reciverId = $reciverId;
        $this->data = $data;
        $this->caller = User::find($senderId);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('handshake.' . $this->reciverId)
        ];
    }
}
