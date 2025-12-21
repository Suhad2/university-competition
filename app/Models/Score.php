<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    protected $fillable = [
        'user_id',
        'test_id',
        'total_score',
        'correct_answers',
        'total_questions',
        'rank',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function test()
    {
        return $this->belongsTo(Test::class);
    }

    // Helper methods
    public function getAccuracyPercentage()
    {
        if ($this->total_questions === 0) {
            return 0;
        }

        return round(($this->correct_answers / $this->total_questions) * 100, 2);
    }

    public function updateScore()
    {
        $answers = $this->user->answers()->where('test_id', $this->test_id)->get();
        $this->correct_answers = $answers->where('is_correct', true)->count();
        $this->total_questions = $answers->count();
        $this->total_score = $this->correct_answers;
        $this->save();
    }
}
