<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\Score;
use App\Models\Answer;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Events\ParticipantReady;

/**
 * QuizController - Handles participant quiz interactions
 * 
 * This controller manages the quiz dashboard for participants,
 * including showing questions, submitting answers, and marking readiness.
 * 
 * All real-time updates are handled via Pusher events.
 */
class QuizController extends Controller
{

      /**
     * Display the quiz page (questions) for active test.
     * 
     * This is a dedicated route for participants to quickly access questions
     * when an active test is in progress.
     */
    public function showQuiz()
    {
        return $this->showDashboard();
    }

    /**
     * Display the quiz dashboard for the current user.
     * 
     * This method loads initial state from the database and passes it to the view.
     * Subsequent updates happen via Pusher real-time events.
     */
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
                $isReady = $currentTest->isUserReady($user->id);

                if ($isReady && $currentTest->currentQuestion) {
                    $question = $currentTest->currentQuestion;
                    $timeRemaining = $currentTest->getTimeRemaining();

                    $existingAnswer = Answer::where('test_id', $currentTest->id)
                        ->where('user_id', $user->id)
                        ->where('question_id', $question->id)
                        ->first();
                }
            }
        }

        return view('quiz.dashboard', compact('user', 'currentTest', 'isReady', 'readyCount', 'question', 'timeRemaining', 'existingAnswer'));
    }

    /**
     * Mark the current user as ready to participate in the test.
     * 
     * Fires ParticipantReady event to notify all participants via Pusher.
     */
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
            
            if ($currentTest->isUserReady($user->id)) {
                return response()->json([
                    'success' => true, 
                    'message' => 'You are already ready to participate!',
                    'readyCount' => $currentTest->getReadyParticipantsCount()
                ]);
            }
            
            // Add participant to the test's ready list
            $currentTest->addReadyParticipant($user->id);
            
            // Get the updated count after adding the user
            $readyCount = $currentTest->getReadyParticipantsCount();

            // Fire the event AFTER saving to database
            event(new ParticipantReady(
                $currentTest,
                $user,
                $readyCount
            ));

            return response()->json([
                'success' => true, 
                'message' => 'You are now ready to participate!',
                'readyCount' => $readyCount
            ]);
                        
        } catch (\Exception $e) {
            Log::error('Error in markAsReady: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred. Please try again.'], 500);
        }
    }

    /**
     * Submit the selected answer for the current question.
     * 
     * Validates the answer and stores it in the database.
     */
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

        $existingAnswer = Answer::where('test_id', $currentTest->id)
            ->where('user_id', $user->id)
            ->where('question_id', $question->id)
            ->first();

        if ($existingAnswer) {
            $existingAnswer->update([
                'selected_answer' => $request->selected_answer,
                'answered_at' => now(),
            ]);
        } else {
            $isCorrect = $request->selected_answer === $question->correct_answer;
            
            Answer::create([
                'user_id' => $user->id,
                'question_id' => $question->id,
                'test_id' => $currentTest->id,
                'selected_answer' => $request->selected_answer,
                'is_correct' => $isCorrect,
                'answered_at' => now(),
            ]);

            $score = $user->scores()->firstOrCreate(['test_id' => $currentTest->id]);
            $score->updateScore();
        }

        return response()->json(['success' => 'Answer submitted successfully']);
    }
}