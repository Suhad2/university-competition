<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'title',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_answer',
        'category',
    ];

    // Relationships
    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function tests()
    {
        return $this->hasMany(Test::class);
    }

    // Helper methods
    public function getOptionsAttribute()
    {
        return [
            'A' => $this->option_a,
            'B' => $this->option_b,
            'C' => $this->option_c,
            'D' => $this->option_d,
        ];
    }
}
