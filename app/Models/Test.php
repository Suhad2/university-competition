<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    protected $fillable = [
        'status',
        'current_question_id',
        'started_at',
        'ended_at',
        'question_start_time',
        'ready_participants',
    ];

    // Relationships
    public function currentQuestion()
    {
        return $this->belongsTo(Question::class, 'current_question_id');
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function scores()
    {
        return $this->hasMany(Score::class);
    }

    // Helper methods
    public function isWaiting()
    {
        return $this->status === 'waiting';
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isEnded()
    {
        return $this->status === 'ended';
    }

    public function getTimeRemaining()
    {
        if (!$this->isActive() || !$this->question_start_time) {
            return 0;
        }

        $elapsed = time() - $this->question_start_time;
        return max(0, 30 - $elapsed); // 30 seconds per question
    }

    // Participant management methods
    public function getReadyParticipants()
    {
        return $this->ready_participants ? json_decode($this->ready_participants, true) : [];
    }

    public function isUserReady($userId)
    {
        $readyParticipants = $this->getReadyParticipants();
        return in_array($userId, $readyParticipants);
    }

    public function addReadyParticipant($userId)
    {
        $readyParticipants = $this->getReadyParticipants();
        if (!in_array($userId, $readyParticipants)) {
            $readyParticipants[] = $userId;
            $this->update(['ready_participants' => json_encode($readyParticipants)]);
        }
    }

    public function getReadyParticipantsCount()
    {
        return count($this->getReadyParticipants());
    }

    public function isWaitingForParticipants()
    {
        return $this->status === 'waiting' && $this->isWaiting();
    }

    public function isAwaitingFirstQuestion()
    {
        return $this->status === 'waiting' && $this->current_question_id === null;
    }
}