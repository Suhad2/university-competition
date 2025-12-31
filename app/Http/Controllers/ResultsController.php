<?php
// app/Http/Controllers/ResultsController.php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Answer;
use Illuminate\Http\Request;

class ResultsController extends Controller
{
    // كل المشاركين
  public function participants()
{
    $participants = User::where('role', 'user')->get();

    return view('results.participants', compact('participants'));
}

    // تفاصيل مشارك واحد
    public function participantDetails(User $user)
    {
        $answers = Answer::with('question')
            ->where('user_id', $user->id)
            ->orderBy('answered_at')
            ->get();

        return view('results.details', [
            'user' => $user,
            'answers' => $answers
        ]);
    }

    // participant يشوف إجاباته
    public function myResults()
    {
        $user = auth()->user();

        $answers = Answer::with('question')
            ->where('user_id', $user->id)
            ->orderBy('answered_at')
            ->get();

        return view('results.details', compact('user', 'answers'));
    }
}
