<?php

namespace App\Events;

use App\Models\Test;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TestEnded implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $test;
    public $redirectUrl;

    /**
     * Create a new event instance.
     */
    public function __construct(Test $test, string $redirectUrl = '/scoreboard')
    {
        $this->test = $test;
        $this->redirectUrl = $redirectUrl;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('quiz-participants'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'test.ended';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'test_id' => $this->test->id,
            'test_status' => 'ended',
            'redirect_url' => $this->redirectUrl,
            'message' => 'Test has ended!',
            'timestamp' => now()->toIso8601String(),
        ];
    }
}