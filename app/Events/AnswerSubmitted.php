<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AnswerSubmitted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public $answer, public $user, public $test)
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
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'university' => $this->user->university,
            ],
            'answer' => [
                'id' => $this->answer->id,
                'selected_answer' => $this->answer->selected_answer,
                'is_correct' => $this->answer->is_correct,
                'answered_at' => $this->answer->answered_at->format('H:i:s'),
            ],
            'score' => [
                'total_score' => $this->user->scores()->where('test_id', $this->test->id)->first()->total_score ?? 0,
            ]
        ];
    }
}
