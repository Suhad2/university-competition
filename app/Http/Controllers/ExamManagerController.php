<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\Question;
use App\Models\User;
use App\Events\QuestionStarted;
use App\Events\TestEnded;

class ExamManagerController extends Controller
{
    public function index()
    {
        $currentTest = Test::latest()->first();
        $users = User::where('role', 'user')->get();
        $totalQuestions = Question::count();
        
        $stats = [
            'waiting_users' => 0,
            'answered_questions' => 0,
            'total_questions' => $totalQuestions,
        ];

        if ($currentTest) {
            if ($currentTest->isActive() && $currentTest->currentQuestion) {
                $answeredCount = \App\Models\Answer::where('test_id', $currentTest->id)
                    ->where('question_id', $currentTest->current_question_id)
                    ->count();
                $stats['answered_questions'] = $answeredCount;
            }
        }

        $stats['waiting_users'] = $users->count();

        return view('exam-manager.dashboard', compact('currentTest', 'users', 'stats'));
    }

    public function startTest(Request $request)
    {
        // End any existing test
        Test::where('status', 'active')->update(['status' => 'ended', 'ended_at' => now()]);
        
        // Create new test
        $test = Test::create([
            'status' => 'waiting',
        ]);

        return redirect()->route('exam-manager.dashboard')->with('success', 'Test started! Waiting for participants...');
    }

    public function nextQuestion(Request $request)
    {
        $currentTest = Test::where('status', 'active')->latest()->first();
        
        if (!$currentTest) {
            return redirect()->route('exam-manager.dashboard')->with('error', 'No active test found!');
        }

        // Get a random question that hasn't been used in this test
        $usedQuestionIds = \App\Models\Answer::where('test_id', $currentTest->id)
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

        // Broadcast question start event
        event(new QuestionStarted($question, $currentTest));

        return redirect()->route('exam-manager.dashboard')->with('success', 'Next question sent to all participants!');
    }

    public function endTest(Request $request)
    {
        $currentTest = Test::where('status', 'active')->latest()->first();
        
        if ($currentTest) {
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
            $scores = \App\Models\Score::where('test_id', $currentTest->id)
                ->orderBy('total_score', 'desc')
                ->get();

            foreach ($scores as $index => $score) {
                $score->update(['rank' => $index + 1]);
            }

            // Broadcast test end event
            event(new TestEnded($currentTest, $scores));

            return redirect()->route('exam-manager.dashboard')->with('success', 'Test ended successfully!');
        }

        return redirect()->route('exam-manager.dashboard')->with('error', 'No active test found!');
    }

    public function showUsersStatus()
    {
        $currentTest = Test::where('status', 'active')->latest()->first();
        $users = User::where('role', 'user')->get();
        
        $userStatus = [];
        
        foreach ($users as $user) {
            $status = 'not_answered';
            $answer = null;
            
            if ($currentTest && $currentTest->currentQuestion) {
                $answer = \App\Models\Answer::where('test_id', $currentTest->id)
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
}
