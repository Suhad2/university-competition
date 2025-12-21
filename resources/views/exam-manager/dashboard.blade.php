@extends('layouts.app')

@section('title', 'Exam Manager Dashboard - University Competition')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-play-circle"></i> Exam Manager Dashboard</h2>
    <div class="text-muted">
        <small>Welcome, {{ Auth::user()->name }}</small>
    </div>
</div>

<!-- Test Status Card -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-info-circle"></i> Current Test Status</h5>
            </div>
            <div class="card-body">
                @if($currentTest)
                    <div class="row">
                        <div class="col-md-3">
                            <div class="stat-card">
                                <h6>Status</h6>
                                @if($currentTest->isWaiting())
                                    <span class="badge bg-warning fs-6">Waiting</span>
                                @elseif($currentTest->isActive())
                                    <span class="badge bg-success fs-6">Active</span>
                                @elseif($currentTest->isEnded())
                                    <span class="badge bg-secondary fs-6">Ended</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card">
                                <h6>Total Questions</h6>
                                <h4>{{ $stats['total_questions'] }}</h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card">
                                <h6>Participants</h6>
                                <h4>{{ $stats['waiting_users'] }}</h4>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card">
                                <h6>Current Question</h6>
                                @if($currentTest->currentQuestion)
                                    <h4>#{{ $currentTest->currentQuestion->id }}</h4>
                                @else
                                    <h4 class="text-muted">None</h4>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center">
                        <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                        <h5>No Active Test</h5>
                        <p class="text-muted">Start a new test to begin the competition</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Control Buttons -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-cogs"></i> Test Controls</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        @if(!$currentTest || $currentTest->isEnded())
                        <form method="POST" action="{{ route('exam-manager.start-test') }}">
                            @csrf
                            <button type="submit" class="btn btn-success btn-lg w-100">
                                <i class="fas fa-play"></i> Start Test
                            </button>
                        </form>
                        @else
                        <button class="btn btn-success btn-lg w-100" disabled>
                            <i class="fas fa-play"></i> Test Running
                        </button>
                        @endif
                    </div>
                    <div class="col-md-4">
                        @if($currentTest && $currentTest->isActive())
                        <form method="POST" action="{{ route('exam-manager.next-question') }}">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-forward"></i> Next Question
                            </button>
                        </form>
                        @else
                        <button class="btn btn-primary btn-lg w-100" disabled>
                            <i class="fas fa-forward"></i> Next Question
                        </button>
                        @endif
                    </div>
                    <div class="col-md-4">
                        @if($currentTest && ($currentTest->isWaiting() || $currentTest->isActive()))
                        <form method="POST" action="{{ route('exam-manager.end-test') }}" 
                              onsubmit="return confirm('Are you sure you want to end the test? This action cannot be undone.')">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-lg w-100">
                                <i class="fas fa-stop"></i> End Test
                            </button>
                        </form>
                        @else
                        <button class="btn btn-danger btn-lg w-100" disabled>
                            <i class="fas fa-stop"></i> End Test
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Current Question Display -->
@if($currentTest && $currentTest->isActive() && $currentTest->currentQuestion)
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-question-circle"></i> Current Question</h5>
            </div>
            <div class="card-body">
                <div class="question-display">
                    <h4>{{ $currentTest->currentQuestion->title }}</h4>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="option-display">
                                <strong>A.</strong> {{ $currentTest->currentQuestion->option_a }}
                            </div>
                            <div class="option-display">
                                <strong>B.</strong> {{ $currentTest->currentQuestion->option_b }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="option-display">
                                <strong>C.</strong> {{ $currentTest->currentQuestion->option_c }}
                            </div>
                            <div class="option-display">
                                <strong>D.</strong> {{ $currentTest->currentQuestion->option_d }}
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="badge bg-info">Correct Answer: {{ $currentTest->currentQuestion->correct_answer }}</span>
                        @if($currentTest->question_start_time)
                        <span class="badge bg-warning ms-2">
                            Time Remaining: {{ $currentTest->getTimeRemaining() }}s
                        </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Participants Status -->
@if($currentTest && $currentTest->isActive())
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-users"></i> Participants Status</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>University</th>
                                <th>Status</th>
                                <th>Answer</th>
                            </tr>
                        </thead>
                        <tbody id="participantsTable">
                            @foreach($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->university ?? 'N/A' }}</td>
                                <td>
                                    @php
                                        $hasAnswered = \App\Models\Answer::where('test_id', $currentTest->id)
                                            ->where('user_id', $user->id)
                                            ->where('question_id', $currentTest->current_question_id ?? 0)
                                            ->exists();
                                    @endphp
                                    @if($hasAnswered)
                                        <span class="badge bg-success">Answered</span>
                                    @else
                                        <span class="badge bg-warning">Waiting</span>
                                    @endif
                                </td>
                                <td>
                                    @if($hasAnswered)
                                        @php
                                            $answer = \App\Models\Answer::where('test_id', $currentTest->id)
                                                ->where('user_id', $user->id)
                                                ->where('question_id', $currentTest->current_question_id ?? 0)
                                                ->first();
                                        @endphp
                                        {{ $answer->selected_answer ?? 'N/A' }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<style>
.stat-card {
    text-align: center;
    padding: 1rem;
}

.stat-card h6 {
    margin-bottom: 0.5rem;
    color: #6c757d;
    font-size: 0.9rem;
}

.stat-card h4 {
    margin-bottom: 0;
    color: #007bff;
}

.option-display {
    padding: 0.5rem;
    margin: 0.25rem 0;
    background: #f8f9fa;
    border-radius: 0.25rem;
}

.question-display {
    max-height: 300px;
    overflow-y: auto;
}
</style>

<!-- Auto-refresh script -->
<script>
setInterval(function() {
    if ({{ $currentTest && $currentTest->isActive() ? 'true' : 'false' }}) {
        location.reload();
    }
}, 10000); // Refresh every 10 seconds
</script>
@endsection
