<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Score;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    public function showDashboard()
    {
        $user = Auth::user();
        $currentTest = Test::latest()->first();
        
        return view('quiz.dashboard', compact('user', 'currentTest'));
    }

    public function showQuiz()
    {
        $user = Auth::user();
        $currentTest = Test::where('status', 'active')->latest()->first();
        
        if (!$currentTest || !$currentTest->currentQuestion) {
            return redirect()->route('dashboard')->with('error', 'No active question found. Please wait for the next question.');
        }

        $question = $currentTest->currentQuestion;
        $timeRemaining = $currentTest->getTimeRemaining();
        
        // Check if user has already answered this question
        $existingAnswer = Answer::where('test_id', $currentTest->id)
            ->where('user_id', $user->id)
            ->where('question_id', $question->id)
            ->first();

        return view('quiz.question', compact('question', 'currentTest', 'timeRemaining', 'existingAnswer'));
    }

    public function submitAnswer(Request $request)
    {
        $user = Auth::user();
        $currentTest = Test::where('status', 'active')->latest()->first();
        
        if (!$currentTest || !$currentTest->currentQuestion) {
            return response()->json(['error' => 'No active question found'], 400);
        }

        $question = $currentTest->currentQuestion;
        $timeRemaining = $currentTest->getTimeRemaining();

        if ($timeRemaining <= 0) {
            return response()->json(['error' => 'Time is up for this question'], 400);
        }

        $request->validate([
            'selected_answer' => 'required|in:A,B,C,D',
        ]);

        // Check if user has already answered this question
        $existingAnswer = Answer::where('test_id', $currentTest->id)
            ->where('user_id', $user->id)
            ->where('question_id', $question->id)
            ->first();

        if ($existingAnswer) {
            // Update existing answer
            $existingAnswer->update([
                'selected_answer' => $request->selected_answer,
                'answered_at' => now(),
            ]);
        } else {
            // Create new answer
            $isCorrect = $request->selected_answer === $question->correct_answer;
            
            Answer::create([
                'user_id' => $user->id,
                'question_id' => $question->id,
                'test_id' => $currentTest->id,
                'selected_answer' => $request->selected_answer,
                'is_correct' => $isCorrect,
                'answered_at' => now(),
            ]);

            // Update or create score
            $score = $user->scores()->firstOrCreate(['test_id' => $currentTest->id]);
            $score->updateScore();
        }

        return response()->json(['success' => 'Answer submitted successfully']);
    }

    public function showWaiting()
    {
        $user = Auth::user();
        $currentTest = Test::latest()->first();
        
        return view('quiz.waiting', compact('user', 'currentTest'));
    }
}
