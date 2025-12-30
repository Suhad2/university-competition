<?php

namespace App\Events;

use App\Models\Test;
use App\Models\Question;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuestionStarted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $question;
    public $test;
    public $questionStartTime;
    public $timeLimit;

    /**
     * Create a new event instance.
     */
    public function __construct(Question $question, Test $test, int $timeLimit = 35)
    {
        $this->question = $question;
        $this->test = $test;
        $this->questionStartTime = $test->question_start_time;
        $this->timeLimit = $timeLimit;
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
        return 'question.started';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'test_id' => $this->test->id,
            'question' => [
                'id' => $this->question->id,
                'title' => $this->question->title,
                'option_a' => $this->question->option_a,
                'option_b' => $this->question->option_b,
                'option_c' => $this->question->option_c,
                'option_d' => $this->question->option_d,
            ],
            'question_start_time' => $this->questionStartTime,
            'time_limit' => $this->timeLimit,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}