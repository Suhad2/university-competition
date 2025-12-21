<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Score;
use App\Models\Test;
use Illuminate\Support\Facades\Auth;

class ScoreboardController extends Controller
{
    public function showScoreboard()
    {
        $currentTest = Test::latest()->first();
        
        if (!$currentTest) {
            return view('scoreboard.empty');
        }

        // Get scores for current test
        $scores = Score::with('user')
            ->where('test_id', $currentTest->id)
            ->orderBy('total_score', 'desc')
            ->orderBy('updated_at', 'asc')
            ->get();

        // Add rank to scores that don't have one yet
        foreach ($scores as $index => $score) {
            if (!$score->rank) {
                $score->update(['rank' => $index + 1]);
                $score->refresh();
            }
        }

        return view('scoreboard.index', compact('scores', 'currentTest'));
    }

    public function getLiveScoreboard()
    {
        $currentTest = Test::latest()->first();
        
        if (!$currentTest) {
            return response()->json(['scores' => []]);
        }

        $scores = Score::with('user')
            ->where('test_id', $currentTest->id)
            ->orderBy('total_score', 'desc')
            ->orderBy('updated_at', 'asc')
            ->get()
            ->map(function ($score) {
                return [
                    'id' => $score->id,
                    'user_name' => $score->user->name,
                    'university' => $score->user->university,
                    'total_score' => $score->total_score,
                    'correct_answers' => $score->correct_answers,
                    'total_questions' => $score->total_questions,
                    'accuracy' => $score->getAccuracyPercentage(),
                    'rank' => $score->rank,
                    'updated_at' => $score->updated_at->format('H:i:s'),
                ];
            });

        return response()->json([
            'scores' => $scores,
            'test_status' => $currentTest->status,
            'current_question' => $currentTest->current_question_id,
        ]);
    }
}
