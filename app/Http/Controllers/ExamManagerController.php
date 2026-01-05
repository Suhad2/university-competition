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
use App\Events\TestUpdated;
use App\Events\ParticipantReady;

/**
 * ExamManagerController - Handles exam management operations
 * 
 * This controller is used by exam managers to:
 * - Start/end tests
 * - Send questions to participants
 * - Monitor participant status
 * 
 * All actions broadcast events via Pusher for real-time updates.
 */
class ExamManagerController extends Controller
{
    /**
     * Display the exam manager dashboard with current test statistics.
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
            $stats['ready_participants'] = $currentTest->getReadyParticipantsCount();
        }

        $stats['waiting_users'] = $users->count();

        return view('exam-manager.dashboard', compact('currentTest', 'users', 'stats'));
    }

    /**
     * Start a new test in waiting status.
     * 
     * Broadcasts TestStarted and TestUpdated events.
     */
    public function startTest(Request $request)
    {
        // End any existing active test
        Test::where('status', 'active')->update(['status' => 'ended', 'ended_at' => now()]);
        
        // Create new test
        $test = Test::create([
            'status' => 'waiting',
        ]);

        // Broadcast test started event
        broadcast(new TestStarted($test, 'Test is ready! Waiting for participants...', 0));

        // Broadcast test updated event
        $participants = $this->getParticipantsData($test);
        $stats = $this->getStats($test);
        broadcast(new TestUpdated($test, $participants, $stats));

        return redirect()->route('exam-manager.dashboard')->with('success', 'Test started! Waiting for participants...');
    }

    /**
     * Start the first question of the test for all ready participants.
     * 
     * Broadcasts QuestionStarted and TestUpdated events.
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

        // Get a random question that hasn't been used
        $question = $this->getNextQuestion($currentTest);

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

        // Broadcast question started event
        broadcast(new QuestionStarted($question, $currentTest));

        // Broadcast test updated event
        $participants = $this->getParticipantsData($currentTest);
        $stats = $this->getStats($currentTest);
        broadcast(new TestUpdated($currentTest, $participants, $stats, [
            'id' => $question->id,
            'title' => $question->title,
            'option_a' => $question->option_a,
            'option_b' => $question->option_b,
            'option_c' => $question->option_c,
            'option_d' => $question->option_d,
            'correct_answer' => $question->correct_answer,
        ]));

        return redirect()->route('exam-manager.dashboard')->with('success', "First question sent to {$readyCount} ready participants!");
    }

    /**
     * Send the next question to all participants.
     * 
     * Broadcasts QuestionStarted and TestUpdated events.
     */
    public function nextQuestion(Request $request)
    {
        $currentTest = Test::where('status', 'active')->latest()->first();
        
        if (!$currentTest) {
            return redirect()->route('exam-manager.dashboard')->with('error', 'No active test found!');
        }

        // Get a random question that hasn't been used
        $question = $this->getNextQuestion($currentTest);

        if (!$question) {
            return redirect()->route('exam-manager.dashboard')->with('error', 'No more questions available!');
        }

        // Update test with new question
        $currentTest->update([
            'current_question_id' => $question->id,
            'question_start_time' => time(),
        ]);

        // Broadcast question started event
        broadcast(new QuestionStarted($question, $currentTest));

        // Broadcast test updated event
        $participants = $this->getParticipantsData($currentTest);
        $stats = $this->getStats($currentTest);
        broadcast(new TestUpdated($currentTest, $participants, $stats, [
            'id' => $question->id,
            'title' => $question->title,
            'option_a' => $question->option_a,
            'option_b' => $question->option_b,
            'option_c' => $question->option_c,
            'option_d' => $question->option_d,
            'correct_answer' => $question->correct_answer,
        ]));

        return redirect()->route('exam-manager.dashboard')->with('success', 'Next question sent to all participants!');
    }

    /**
     * End the current test and calculate final scores.
     * 
     * Broadcasts TestEnded and TestUpdated events.
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

            // Broadcast test ended event
            broadcast(new TestEnded($currentTest, '/scoreboard'));

            // Broadcast test updated event
            $participants = $this->getParticipantsData($currentTest);
            $stats = $this->getStats($currentTest);
            broadcast(new TestUpdated($currentTest, $participants, $stats));

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
     * Get the next unused question for the test.
     */
    private function getNextQuestion(Test $test): ?Question
    {
        $usedQuestionIds = Answer::where('test_id', $test->id)
            ->pluck('question_id')
            ->toArray();

        return Question::whereNotIn('id', $usedQuestionIds)->inRandomOrder()->first();
    }

    /**
     * Get participants data for broadcast.
     */
    private function getParticipantsData($test)
    {
        $readyParticipants = $test->getReadyParticipants();
        $users = User::where('role', 'user')->get();
        
        return $users->map(function ($user) use ($test, $readyParticipants) {
            $hasAnswered = false;
            $selectedAnswer = null;
            
            if ($test->currentQuestion) {
                $answer = Answer::where('test_id', $test->id)
                    ->where('user_id', $user->id)
                    ->where('question_id', $test->current_question_id)
                    ->first();
                
                if ($answer) {
                    $hasAnswered = true;
                    $selectedAnswer = $answer->selected_answer;
                }
            }
            
            return [
                'id' => $user->id,
                'name' => $user->name,
                'university' => $user->university,
                'is_ready' => in_array($user->id, $readyParticipants),
                'has_answered' => $hasAnswered,
                'selected_answer' => $selectedAnswer,
            ];
        })->toArray();
    }

    /**
     * Get stats for broadcast.
     */
    private function getStats($test)
    {
        return [
            'ready_participants' => $test->getReadyParticipantsCount(),
            'answered_questions' => $test->currentQuestion 
                ? Answer::where('test_id', $test->id)
                    ->where('question_id', $test->current_question_id)
                    ->count()
                : 0,
            'total_questions' => Question::count(),
        ];
    }
}