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
}
