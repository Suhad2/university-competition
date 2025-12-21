<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TestEnded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public $test, public $scores)
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('quiz'),
        ];
    }

    public function broadcastWith()
    {
        return [
            'test' => [
                'id' => $this->test->id,
                'ended_at' => $this->test->ended_at->format('Y-m-d H:i:s'),
            ],
            'final_scores' => $this->scores->map(function ($score) {
                return [
                    'user_name' => $score->user->name,
                    'university' => $score->user->university,
                    'total_score' => $score->total_score,
                    'rank' => $score->rank,
                    'accuracy' => $score->getAccuracyPercentage(),
                ];
            })->values(),
            'winner' => $this->scores->first() ? [
                'user_name' => $this->scores->first()->user->name,
                'university' => $this->scores->first()->user->university,
                'total_score' => $this->scores->first()->total_score,
            ] : null
        ];
    }
}
