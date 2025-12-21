@extends('layouts.app')

@section('title', 'Quiz Dashboard - University Competition')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow">
            <div class="card-header bg-info text-white text-center">
                <h4><i class="fas fa-graduation-cap"></i> Welcome to University Competition</h4>
            </div>
            <div class="card-body text-center">
                <div class="mb-4">
                    <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                    <h3>Please wait for the test to start and the first question to appear</h3>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5><i class="fas fa-user"></i> Participant</h5>
                                <p class="mb-1"><strong>Name:</strong> {{ $user->name }}</p>
                                @if($user->university)
                                <p class="mb-1"><strong>University:</strong> {{ $user->university }}</p>
                                @endif
                                <p class="mb-0"><strong>Role:</strong> {{ ucfirst(str_replace('_', ' ', $user->role)) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5><i class="fas fa-info-circle"></i> Test Status</h5>
                                @if($currentTest)
                                    @if($currentTest->isWaiting())
                                        <p class="mb-1"><span class="badge bg-warning">Waiting</span></p>
                                        <p class="text-muted mb-0">Test is prepared and waiting to start</p>
                                    @elseif($currentTest->isActive())
                                        <p class="mb-1"><span class="badge bg-success">Active</span></p>
                                        <p class="text-muted mb-0">Test is currently in progress</p>
                                        <a href="{{ route('quiz') }}" class="btn btn-sm btn-primary mt-2">
                                            <i class="fas fa-play"></i> Join Test
                                        </a>
                                    @elseif($currentTest->isEnded())
                                        <p class="mb-1"><span class="badge bg-secondary">Ended</span></p>
                                        <p class="text-muted mb-0">Test has been completed</p>
                                        <a href="{{ route('scoreboard') }}" class="btn btn-sm btn-success mt-2">
                                            <i class="fas fa-trophy"></i> View Results
                                        </a>
                                    @endif
                                @else
                                    <p class="mb-1"><span class="badge bg-secondary">No Test</span></p>
                                    <p class="text-muted mb-0">No test is currently scheduled</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-lightbulb"></i> Instructions:</h6>
                        <ul class="text-start mb-0">
                            <li>Wait for the exam manager to start the test</li>
                            <li>Each question will have a 30-second timer</li>
                            <li>Select your answer before the timer expires</li>
                            <li>You can change your answer until the timer runs out</li>
                            <li>After each question, the correct answer will be revealed</li>
                            <li>Points are awarded for correct answers only</li>
                        </ul>
                    </div>
                </div>

                @if($currentTest && $currentTest->isActive())
                <div class="d-grid">
                    <a href="{{ route('quiz') }}" class="btn btn-success btn-lg">
                        <i class="fas fa-play"></i> Start Quiz
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Auto-refresh script for real-time updates -->
<script>
    setInterval(function() {
        location.reload();
    }, 30000); // Refresh every 30 seconds
</script>
@endsection
