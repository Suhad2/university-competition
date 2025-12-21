<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\QuestionsImport;

class AdminController extends Controller
{
    public function index()
    {
        $totalQuestions = Question::count();
        $totalUsers = \App\Models\User::count();
        $recentQuestions = Question::latest()->take(5)->get();

        return view('admin.dashboard', compact('totalQuestions', 'totalUsers', 'recentQuestions'));
    }

    public function showQuestions()
    {
        $questions = Question::latest()->paginate(10);
        return view('admin.questions.index', compact('questions'));
    }

    public function createQuestion()
    {
        return view('admin.questions.create');
    }

    public function storeQuestion(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'option_a' => 'required',
            'option_b' => 'required',
            'option_c' => 'required',
            'option_d' => 'required',
            'correct_answer' => 'required|in:A,B,C,D',
            'category' => 'nullable',
        ]);

        Question::create($request->all());

        return redirect()->route('admin.questions')->with('success', 'Question created successfully!');
    }

    public function editQuestion(Question $question)
    {
        return view('admin.questions.edit', compact('question'));
    }

    public function updateQuestion(Request $request, Question $question)
    {
        $request->validate([
            'title' => 'required',
            'option_a' => 'required',
            'option_b' => 'required',
            'option_c' => 'required',
            'option_d' => 'required',
            'correct_answer' => 'required|in:A,B,C,D',
            'category' => 'nullable',
        ]);

        $question->update($request->all());

        return redirect()->route('admin.questions')->with('success', 'Question updated successfully!');
    }

    public function deleteQuestion(Question $question)
    {
        $question->delete();
        return redirect()->route('admin.questions')->with('success', 'Question deleted successfully!');
    }

    public function importQuestions(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        try {
            Excel::import(new QuestionsImport, $request->file('file'));
            return redirect()->route('admin.questions')->with('success', 'Questions imported successfully!');
        } catch (\Exception $e) {
            return redirect()->route('admin.questions')->with('error', 'Error importing questions: ' . $e->getMessage());
        }
    }
}
