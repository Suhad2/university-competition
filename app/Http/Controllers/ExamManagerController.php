<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\Question;
use App\Models\User;
use App\Models\Answer;
use App\Models\Score;
use App\Events\TestStarted;
use App\Events\QuestionStarted;
use App\Events\TestEnded;

class ExamManagerController extends Controller
{
    /**
     * Display the exam manager dashboard.
     * This is the main index method that was missing and causing the error.
     */
    public function index()
    {
        $currentTest = Test::latest()->first();
        $users = User::where('role', 'user')->get();
        $totalQuestions = Question::count();
        
        $stats = [
            'waiting_users' => 0,
            'ready_participants' => 0,
            'answered_questions' => 0,
            'total_questions' => $totalQuestions,
        ];

        if ($currentTest) {
            if ($currentTest->currentQuestion) {
                $answeredCount = Answer::where('test_id', $currentTest->id)
                    ->where('question_id', $currentTest->current_question_id)
                    ->count();
                $stats['answered_questions'] = $answeredCount;
            }
            // Always show ready participants count if there are any
            $stats['ready_participants'] = $currentTest->getReadyParticipantsCount();
        }

        $stats['waiting_users'] = $users->count();

        return view('exam-manager.dashboard', compact('currentTest', 'users', 'stats'));
    }

    /**
     * Start a new test.
     * Creates a new test in 'waiting' status and broadcasts to participants.
     */
    public function startTest(Request $request)
    {
        // End any existing active test
        Test::where('status', 'active')->update(['status' => 'ended', 'ended_at' => now()]);
        
        // Create new test
        $test = Test::create([
            'status' => 'waiting',
        ]);

        // Broadcast test started event to all participants
        if (class_exists(\App\Events\TestStarted::class)) {
            broadcast(new TestStarted($test, 'Test is ready! Waiting for participants...', 0));
        }

        return redirect()->route('exam-manager.dashboard')->with('success', 'Test started! Waiting for participants...');
    }

    /**
     * Start the first question of the test.
     * Only works when test is in 'waiting' status.
     */
    public function startFirstQuestion(Request $request)
    {
        $currentTest = Test::where('status', 'waiting')->latest()->first();
        
        if (!$currentTest) {
            return redirect()->route('exam-manager.dashboard')->with('error', 'No test found! Please start a test first.');
        }

        $readyCount = $currentTest->getReadyParticipantsCount();
        if ($readyCount === 0) {
            return redirect()->route('exam-manager.dashboard')->with('error', 'No participants are ready! Wait for students to click "I\'m Ready" first.');
        }

        // Get a random question that hasn't been used in this test
        $usedQuestionIds = Answer::where('test_id', $currentTest->id)
            ->pluck('question_id')
            ->toArray();

        $question = Question::whereNotIn('id', $usedQuestionIds)->inRandomOrder()->first();

        if (!$question) {
            return redirect()->route('exam-manager.dashboard')->with('error', 'No questions available!');
        }

        // Update test with new question and set to active
        $currentTest->update([
            'current_question_id' => $question->id,
            'question_start_time' => time(),
            'status' => 'active',
            'started_at' => now(),
        ]);

        // Broadcast question started event to all participants
        if (class_exists(\App\Events\QuestionStarted::class)) {
            broadcast(new QuestionStarted($question, $currentTest));
        }

        return redirect()->route('exam-manager.dashboard')->with('success', "First question sent to {$readyCount} ready participants!");
    }

    /**
     * Send the next question to all participants.
     * Only works when test is active.
     */
    public function nextQuestion(Request $request)
    {
        $currentTest = Test::where('status', 'active')->latest()->first();
        
        if (!$currentTest) {
            return redirect()->route('exam-manager.dashboard')->with('error', 'No active test found!');
        }

        // Get a random question that hasn't been used in this test
        $usedQuestionIds = Answer::where('test_id', $currentTest->id)
            ->pluck('question_id')
            ->toArray();

        $question = Question::whereNotIn('id', $usedQuestionIds)->inRandomOrder()->first();

        if (!$question) {
            return redirect()->route('exam-manager.dashboard')->with('error', 'No more questions available!');
        }

        // Update test with new question
        $currentTest->update([
            'current_question_id' => $question->id,
            'question_start_time' => time(),
        ]);

        // Broadcast question started event to all participants
        if (class_exists(\App\Events\QuestionStarted::class)) {
            broadcast(new QuestionStarted($question, $currentTest));
        }

        return redirect()->route('exam-manager.dashboard')->with('success', 'Next question sent to all participants!');
    }

    /**
     * End the current test.
     * Calculates scores and assigns ranks to all participants.
     */
    public function endTest(Request $request)
    {
        $currentTest = Test::whereIn('status', ['waiting', 'active'])->latest()->first();
        
        if ($currentTest) {
            $testId = $currentTest->id;
            
            $currentTest->update([
                'status' => 'ended',
                'ended_at' => now(),
                'current_question_id' => null,
                'question_start_time' => null,
            ]);

            // Calculate final scores
            $users = User::where('role', 'user')->get();
            foreach ($users as $user) {
                $score = $user->scores()->firstOrCreate(['test_id' => $currentTest->id]);
                $score->updateScore();
            }

            // Assign ranks
            $scores = Score::where('test_id', $currentTest->id)
                ->orderBy('total_score', 'desc')
                ->get();

            foreach ($scores as $index => $score) {
                $score->update(['rank' => $index + 1]);
            }

            // Broadcast test ended event to all participants
            if (class_exists(\App\Events\TestEnded::class)) {
                broadcast(new TestEnded($currentTest, '/scoreboard'));
            }

            return redirect()->route('exam-manager.dashboard')->with('success', 'Test ended successfully!');
        }

        return redirect()->route('exam-manager.dashboard')->with('error', 'No test found! Please start a test first.');
    }

    /**
     * Get the status of all users in the current test.
     * Returns JSON response with user status information.
     */
    public function showUsersStatus()
    {
        $currentTest = Test::whereIn('status', ['waiting', 'active'])->latest()->first();
        $users = User::where('role', 'user')->get();
        
        $userStatus = [];
        
        foreach ($users as $user) {
            $status = 'not_answered';
            $answer = null;
            
            if ($currentTest && $currentTest->currentQuestion) {
                $answer = Answer::where('test_id', $currentTest->id)
                    ->where('user_id', $user->id)
                    ->where('question_id', $currentTest->current_question_id)
                    ->first();
                
                if ($answer) {
                    $status = 'answered';
                }
            }
            
            $userStatus[] = [
                'user' => $user,
                'status' => $status,
                'answer' => $answer,
            ];
        }

        return response()->json($userStatus);
    }

    /**
     * Trigger manual status update to participants.
     * This endpoint can be called via AJAX as a fallback for cross-device scenarios.
     */
    public function triggerStatusUpdate(Request $request)
    {
        $currentTest = Test::latest()->first();
        
        if ($currentTest) {
            $data = [];
            
            if ($currentTest->status === 'waiting') {
                $data = [
                    'testStatus' => 'waiting',
                    'ready_count' => $currentTest->getReadyParticipantsCount(),
                ];
            } elseif ($currentTest->status === 'active' && $currentTest->currentQuestion) {
                $question = $currentTest->currentQuestion;
                $data = [
                    'testStatus' => 'active',
                    'questionData' => [
                        'id' => $question->id,
                        'title' => $question->title,
                        'option_a' => $question->option_a,
                        'option_b' => $question->option_b,
                        'option_c' => $question->option_c,
                        'option_d' => $question->option_d,
                    ],
                    'questionStartTime' => $currentTest->question_start_time,
                    'timeLimit' => 35,
                ];
            } elseif ($currentTest->status === 'ended') {
                $data = [
                    'testStatus' => 'ended',
                ];
            }
            
            return response()->json([
                'success' => true, 
                'message' => 'Update triggered',
                'test_status' => $currentTest->status,
                'data' => $data
            ]);
        }
        
        return response()->json(['success' => false, 'message' => 'No test found']);
    }
}