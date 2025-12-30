<?php

namespace App\Events;

use App\Models\Test;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class ParticipantReady implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public $testId;
    public $userName;
    public $readyCount;

    public function __construct(Test $test, User $user, int $readyCount)
    {
        $this->testId = $test->id;
        $this->userName = $user->name;
        $this->readyCount = $readyCount;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('quiz-participants'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'participant.ready';
    }

    public function broadcastWith(): array
    {
        return [
            'test_id' => $this->testId,
            'user_name' => $this->userName,
            'ready_count' => $this->readyCount,
        ];
    }
}
