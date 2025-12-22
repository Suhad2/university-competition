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
        $isReady = false;
        $readyCount = 0;
        $question = null;
        $timeRemaining = null;
        $existingAnswer = null;

        if ($currentTest) {
            if ($currentTest->status === 'waiting') {
                $isReady = $currentTest->isUserReady($user->id);
                $readyCount = $currentTest->getReadyParticipantsCount();
            } elseif ($currentTest->status === 'active') {
                // Check if user is a ready participant
                $isReady = $currentTest->isUserReady($user->id);

                if ($isReady && $currentTest->currentQuestion) {
                    $question = $currentTest->currentQuestion;
                    $timeRemaining = $currentTest->getTimeRemaining();

                    // Check if user has already answered this question
                    $existingAnswer = Answer::where('test_id', $currentTest->id)
                        ->where('user_id', $user->id)
                        ->where('question_id', $question->id)
                        ->first();
                }
            }
        }

        return view('quiz.dashboard', compact('user', 'currentTest', 'isReady', 'readyCount', 'question', 'timeRemaining', 'existingAnswer'));
    }

    public function markAsReady(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }
            
            $currentTest = Test::where('status', 'waiting')->latest()->first();
            
            if (!$currentTest) {
                return response()->json(['error' => 'No test available'], 400);
            }
            
            // Check if user is already ready
            if ($currentTest->isUserReady($user->id)) {
                return response()->json([
                    'success' => true, 
                    'message' => 'You are already ready to participate!',
                    'readyCount' => $currentTest->getReadyParticipantsCount()
                ]);
            }
            
            $currentTest->addReadyParticipant($user->id);
            
            return response()->json([
                'success' => true, 
                'message' => 'You are now ready to participate!',
                'readyCount' => $currentTest->getReadyParticipantsCount()
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error in markAsReady: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred. Please try again.'], 500);
        }
    }

    public function showQuiz()
    {
        // Redirect to dashboard - question will appear there directly
        return redirect()->route('dashboard');
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