@extends('layouts.app')

@section('title', 'Admin Dashboard - University Competition')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-cog"></i> Admin Dashboard</h2>
    <a href="{{ route('admin.questions') }}" class="btn btn-primary">
        <i class="fas fa-list"></i> Manage Questions
    </a>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Total Questions</h5>
                        <h2 class="mb-0">{{ $totalQuestions }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-question-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Total Users</h5>
                        <h2 class="mb-0">{{ $totalUsers }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Active Test</h5>
                        <h2 class="mb-0">{{ \App\Models\Test::where('status', 'active')->count() }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-play-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-clock"></i> Recent Questions</h5>
            </div>
            <div class="card-body">
                @if($recentQuestions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Question</th>
                                    <th>Category</th>
                                    <th>Correct Answer</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentQuestions as $question)
                                <tr>
                                    <td>{{ Str::limit($question->title, 50) }}</td>
                                    <td>{{ $question->category ?? 'N/A' }}</td>
                                    <td><span class="badge bg-success">{{ $question->correct_answer }}</span></td>
                                    <td>{{ $question->created_at->diffForHumans() }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">No questions created yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-tools"></i> Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <a href="{{ route('admin.questions.create') }}" class="btn btn-primary btn-block w-100 mb-2">
                            <i class="fas fa-plus"></i> Add New Question
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('admin.questions') }}" class="btn btn-info btn-block w-100 mb-2">
                            <i class="fas fa-list"></i> View All Questions
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('scoreboard') }}" class="btn btn-success btn-block w-100 mb-2">
                            <i class="fas fa-trophy"></i> View Scoreboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
