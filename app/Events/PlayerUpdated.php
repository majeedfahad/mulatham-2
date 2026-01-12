<?php

namespace App\Events;

use App\Models\Room;
use App\Models\RoomPlayer;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlayerUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Room $room;
    public string $action;
    public ?RoomPlayer $player;

    /**
     * Create a new event instance.
     */
    public function __construct(Room $room, string $action, ?RoomPlayer $player = null)
    {
        $this->room = $room;
        $this->action = $action;
        $this->player = $player;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('room.' . $this->room->code),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'player.updated';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        $players = $this->room->players()->get()->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'status' => $p->status,
                'is_host' => $p->is_host,
            ];
        });

        return [
            'action' => $this->action,
            'room_code' => $this->room->code,
            'players' => $players->toArray(),
            'player_count' => $players->count(),
            'ready_count' => $players->where('status', 'ready')->count(),
            'waiting_count' => $players->where('status', 'waiting')->count(),
            'timestamp' => now()->toISOString(),
        ];
    }
}
