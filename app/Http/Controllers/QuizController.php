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

    /**
     * Get real-time status for polling fallback
     */
    public function getRealtimeStatus()
    {
        $user = Auth::user();
        $currentTest = Test::latest()->first();

        $status = [
            'test_waiting' => false,
            'test_active' => false,
            'user_is_ready' => false,
            'has_question' => false,
            'question_data' => null,
            'current_question_id' => null,
            'exam_ended' => false,
            'redirect_url' => null,
            'ready_count' => 0,
            'html' => null,
        ];

        if ($currentTest) {
            // Check if test has ended
            if ($currentTest->status === 'ended') {
                $status['exam_ended'] = true;
                $status['redirect_url'] = route('dashboard');
            }

            // Check if test is in waiting status
            if ($currentTest->status === 'waiting') {
                $status['test_waiting'] = true;
                $status['user_is_ready'] = $currentTest->isUserReady($user->id);
                $status['ready_count'] = $currentTest->getReadyParticipantsCount();
            }
            
            // Always return current question ID if available
            if ($currentTest->currentQuestion) {
                $status['current_question_id'] = $currentTest->currentQuestion->id;
            }
            
            // Check if test is active and user is ready
            if ($currentTest->status === 'active' && $currentTest->isUserReady($user->id)) {
                $status['test_active'] = true;
                
                if ($currentTest->currentQuestion) {
                    $question = $currentTest->currentQuestion;
                    $status['has_question'] = true;
                    $status['question_data'] = [
                        'id' => $question->id,
                        'title' => $question->title,
                        'option_a' => $question->option_a,
                        'option_b' => $question->option_b,
                        'option_c' => $question->option_c,
                        'option_d' => $question->option_d,
                        'question_start_time' => $currentTest->question_start_time,
                    ];

                    // Check if user has already answered this question
                    $existingAnswer = Answer::where('test_id', $currentTest->id)
                        ->where('user_id', $user->id)
                        ->where('question_id', $question->id)
                        ->first();

                    // Generate HTML only if user hasn't answered yet
                    if (!$existingAnswer) {
                        $status['html'] = view('quiz.partials.question-container', [
                            'question' => $question,
                            'currentTest' => $currentTest,
                        ])->render();
                    }
                }
            }
        }

        return response()->json($status);
    }

    /**
     * Get question HTML for partial page update
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

            // Check if user is ready
            if (!$currentTest->isUserReady($user->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not ready for this test'
                ], 403);
            }

            // Check if user has already answered
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

    /**
     * Check test status for partial updates
     */
    public function checkTestStatus(Request $request)
    {
        try {
            $user = Auth::user();
            $currentTest = Test::latest()->first();

            $response = [
                'has_test' => false,
                'test_status' => null,
                'user_ready' => false,
                'has_question' => false,
                'question_id' => null,
                'exam_ended' => false,
                'redirect_url' => null,
            ];

            if ($currentTest) {
                $response['has_test'] = true;
                $response['test_status'] = $currentTest->status;
                $response['user_ready'] = $currentTest->isUserReady($user->id);

                if ($currentTest->status === 'ended') {
                    $response['exam_ended'] = true;
                    $response['redirect_url'] = route('dashboard');
                } elseif ($currentTest->status === 'active') {
                    if ($currentTest->currentQuestion) {
                        $response['has_question'] = true;
                        $response['question_id'] = $currentTest->currentQuestion->id;
                    }
                }
            }

            return response()->json($response);

        } catch (\Exception $e) {
            \Log::error('Error in checkTestStatus: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred'
            ], 500);
        }
    }
}