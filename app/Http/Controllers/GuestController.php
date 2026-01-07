<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\User;
use App\Models\Answer;
use App\Models\Question;
use App\Models\Score;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GuestController extends Controller
{
       /**
     * Display the guest landing page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $currentTest = Test::latest()->first();
        
        // Get all users (participants)
        $users = User::where('role', 'participant')->get();
        
        // Get scores for current test
        $scores = collect();
        if ($currentTest) {
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
        }
        
        // Calculate stats
        $stats = [
            'total_users' => $users->count(),
            'ready_participants' => 0,
            'total_questions' => Question::count() ?? 0,
            'answered_questions' => 0
        ];
        
        if ($currentTest) {
            $readyParticipants = $currentTest->getReadyParticipants() ?? [];
            $stats['ready_participants'] = count($readyParticipants);
            
            if ($currentTest->current_question_id) {
                $stats['answered_questions'] = Answer::where('test_id', $currentTest->id)
                    ->where('question_id', $currentTest->current_question_id)
                    ->count();
            }
        }
        
        return view('index', compact('currentTest', 'users', 'scores', 'stats'));
    }
    /**
     * Get competition data for real-time updates.
     *
     * @return JsonResponse
     */
    public function getData(): JsonResponse
    {
        // ... (getData method remains unchanged)
        try {
            // Get the current test
            $currentTest = Test::latest()->first();

            // If no test exists, return empty data
            if (!$currentTest) {
                return response()->json([
                    'stats' => [
                        'total_users' => 0,
                        'ready_participants' => 0,
                        'total_questions' => 0,
                        'answered_questions' => 0
                    ],
                    'participants' => [],
                    'currentQuestion' => null,
                    'currentTest' => null,
                    'scoreboard' => []
                ]);
            }

            // Get all participant users
            $users = User::where('role', 'participant')->get();

            // Get ready participants from the test
            $readyParticipants = $currentTest->getReadyParticipants() ?? [];
            $readyCount = count($readyParticipants);

            // Get current question if exists
            $currentQuestion = null;
            if ($currentTest->current_question_id) {
                $currentQuestion = Question::find($currentTest->current_question_id);
            }

            // Build participants data
            $participants = [];
            foreach ($users as $user) {
                // Only include ready participants if test is waiting
                if ($currentTest->isWaiting() && !in_array($user->id, $readyParticipants)) {
                    continue;
                }

                // Check if user has answered current question
                $hasAnswered = false;
                $selectedAnswer = null;

                if ($currentTest->current_question_id && $currentTest->isActive()) {
                    $answer = Answer::where('test_id', $currentTest->id)
                        ->where('user_id', $user->id)
                        ->where('question_id', $currentTest->current_question_id)
                        ->first();

                    if ($answer) {
                        $hasAnswered = true;
                        $selectedAnswer = $answer->selected_answer;
                    }
                }

                // Determine status
                $status = 'waiting';
                if ($currentTest->isWaiting()) {
                    $status = 'ready';
                } elseif ($currentTest->isActive() && in_array($user->id, $readyParticipants)) {
                    $status = $hasAnswered ? 'answered' : 'waiting';
                } elseif ($currentTest->isEnded()) {
                    $status = 'ended';
                }

                // Get user's score
                $userScore = Score::where('test_id', $currentTest->id)
                    ->where('user_id', $user->id)
                    ->value('score') ?? 0;

                $participants[] = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'university' => $user->university ?? 'N/A',
                    'status' => $status,
                    'has_answered' => $hasAnswered,
                    'selected_answer' => $selectedAnswer,
                    'score' => $userScore
                ];
            }

            // Get current question data
            $currentQuestionData = null;
            if ($currentQuestion && $currentTest->isActive()) {
                $currentQuestionData = [
                    'id' => $currentQuestion->id,
                    'question_number' => $currentQuestion->question_number ?? 1,
                    'title' => $currentQuestion->title,
                    'option_a' => $currentQuestion->option_a,
                    'option_b' => $currentQuestion->option_b,
                    'option_c' => $currentQuestion->option_c,
                    'option_d' => $currentQuestion->option_d,
                    'correct_answer' => $currentQuestion->correct_answer,
                    'time_limit' => 35
                ];
            }

            // Get scoreboard data (for ended tests)
            $scoreboard = [];
            if ($currentTest->isEnded()) {
                usort($participants, function($a, $b) {
                    return ($b['score'] ?? 0) - ($a['score'] ?? 0);
                });
                $scoreboard = $participants;
            }

            // Calculate answered questions count
            $answeredQuestionsCount = 0;
            if ($currentQuestion) {
                $answeredQuestionsCount = Answer::where('test_id', $currentTest->id)
                    ->where('question_id', $currentTest->current_question_id)
                    ->count();
            }

            // Build response
            $response = [
                'stats' => [
                    'total_users' => $users->count(),
                    'ready_participants' => $readyCount,
                    'total_questions' => Question::count() ?? 0,
                    'answered_questions' => $answeredQuestionsCount
                ],
                'participants' => $participants,
                'currentQuestion' => $currentQuestionData,
                'scoreboard' => $scoreboard
            ];

            // Add test status
            if ($currentTest->isWaiting()) {
                $response['currentTest'] = [
                    'status' => 'waiting',
                    'is_waiting' => true,
                    'is_active' => false,
                    'is_ended' => false
                ];
            } elseif ($currentTest->isActive()) {
                $questionStartTime = $currentTest->question_start_time 
                    ? strtotime($currentTest->question_start_time) 
                    : time();

                $response['currentTest'] = [
                    'status' => 'active',
                    'is_waiting' => false,
                    'is_active' => true,
                    'is_ended' => false,
                    'current_question_id' => $currentTest->current_question_id,
                    'question_start_time' => $questionStartTime,
                    'time_limit' => 35
                ];
            } elseif ($currentTest->isEnded()) {
                $response['currentTest'] = [
                    'status' => 'ended',
                    'is_waiting' => false,
                    'is_active' => false,
                    'is_ended' => true
                ];
            }

            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch competition data',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}