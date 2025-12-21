<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuestionStarted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public $question, public $test)
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
            'question' => [
                'id' => $this->question->id,
                'title' => $this->question->title,
                'option_a' => $this->question->option_a,
                'option_b' => $this->question->option_b,
                'option_c' => $this->question->option_c,
                'option_d' => $this->question->option_d,
            ],
            'test' => [
                'id' => $this->test->id,
                'question_start_time' => $this->test->question_start_time,
            ]
        ];
    }
}
