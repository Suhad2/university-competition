<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Question;

class AnswerReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $userName;
    public $university;
    public $questionId;
    public $selectedAnswer;
    public $answeredAt;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, $questionId, $selectedAnswer)
    {
        $this->userId = $user->id;
        $this->userName = $user->name;
        $this->university = $user->university;
        $this->questionId = $questionId;
        $this->selectedAnswer = $selectedAnswer;
        $this->answeredAt = now()->toIso8601String();
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
        return 'answer.received';
    }
}