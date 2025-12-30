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
            
                           // Add participant to the test's ready list FIRST
            $currentTest->addReadyParticipant($user->id);
            
            // Get the UPDATED count after adding the user
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

    public function showQuiz()
    {
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

    public function showWaiting()
    {
        $user = Auth::user();
        $currentTest = Test::latest()->first();

        return view('quiz.waiting', compact('user', 'currentTest'));
    }

    /**
     * Get current test status for initial page load
     */
    public function getCurrentStatus()
    {
        $user = Auth::user();
        $currentTest = Test::latest()->first();

        $status = [
            'has_test' => false,
            'test_status' => null,
            'test_id' => null,
            'user_ready' => false,
            'ready_count' => 0,
            'has_question' => false,
            'question' => null,
            'question_start_time' => null,
            'time_limit' => 35,
            'exam_ended' => false,
        ];

        if ($currentTest) {
            $status['has_test'] = true;
            $status['test_status'] = $currentTest->status;
            $status['test_id'] = $currentTest->id;

            if ($currentTest->status === 'waiting') {
                $status['user_ready'] = $currentTest->isUserReady($user->id);
                $status['ready_count'] = $currentTest->getReadyParticipantsCount();
            } elseif ($currentTest->status === 'active') {
                $status['user_ready'] = $currentTest->isUserReady($user->id);

                if ($status['user_ready'] && $currentTest->currentQuestion) {
                    $question = $currentTest->currentQuestion;
                    $status['has_question'] = true;
                    $status['question'] = [
                        'id' => $question->id,
                        'title' => $question->title,
                        'option_a' => $question->option_a,
                        'option_b' => $question->option_b,
                        'option_c' => $question->option_c,
                        'option_d' => $question->option_d,
                    ];
                    $status['question_start_time'] = $currentTest->question_start_time;
                }
            } elseif ($currentTest->status === 'ended') {
                $status['exam_ended'] = true;
            }
        }

        return response()->json($status);
    }

    /**
     * Get question HTML for partial updates
     */
    public function getQuestionHtml(Request $request)
    {
        try {
            $user = Auth::user();
            $currentTest = Test::where('status', 'active')->latest()->first();

            if (!$currentTest || !$currentTest->currentQuestion) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active question found'
                ], 400);
            }

            if (!$currentTest->isUserReady($user->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not ready for this test'
                ], 403);
            }

            $existingAnswer = Answer::where('test_id', $currentTest->id)
                ->where('user_id', $user->id)
                ->where('question_id', $currentTest->currentQuestion->id)
                ->first();

            if ($existingAnswer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Already answered',
                    'waiting' => true
                ]);
            }

            $question = $currentTest->currentQuestion;
            
            return response()->json([
                'success' => true,
                'html' => view('quiz.partials.question-container', [
                    'question' => $question,
                    'currentTest' => $currentTest,
                ])->render(),
                'question_id' => $question->id,
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in getQuestionHtml: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while loading question'
            ], 500);
        }
    }
}
