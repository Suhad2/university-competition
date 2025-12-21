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
                @if($currentTest && $currentTest->isWaiting())
                <div class="mb-4">
                    <i class="fas fa-hourglass-half fa-3x text-warning mb-3"></i>
                    <h3>Test is Ready!</h3>
                    <p class="lead">The exam manager has prepared the test. {{ $readyCount }} participants are ready.</p>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>What to do:</strong>
                        <ul class="text-start mb-0 mt-2">
                            <li>Click "I'm Ready" to confirm your participation</li>
                            <li>Stay on this page - it will automatically refresh</li>
                            <li>Keep your browser open</li>
                            <li>The first question will appear when the exam manager starts it</li>
                            <li>Be prepared to answer quickly (30 seconds per question)</li>
                        </ul>
                    </div>
                </div>
                @else
                <div class="mb-4">
                    <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                    <h3>Please wait for the test to start and the first question to appear</h3>
                </div>
                @endif

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
                                        @if($isReady)
                                            <button class="btn btn-sm btn-success mt-2" disabled>
                                                <i class="fas fa-check"></i> I'm Ready!
                                            </button>
                                        @else
                                            <button class="btn btn-sm btn-warning mt-2" onclick="markAsReady()">
                                                <i class="fas fa-hand-paper"></i> I'm Ready!
                                            </button>
                                        @endif
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
                            <li><strong>Stay on this page</strong> - don't close or navigate away</li>
                            <li>The page will automatically refresh to show updates</li>
                            <li>When the exam manager starts questions, click "Join Test Now"</li>
                            <li>Each question has a 30-second timer</li>
                            <li>Select your answer before the timer expires</li>
                            <li>You can change your answer until the timer runs out</li>
                            <li>Points are awarded for correct answers only</li>
                        </ul>
                    </div>
                </div>

                @if($currentTest && $currentTest->isActive())
                <div class="d-grid">
                    <a href="{{ route('quiz') }}" class="btn btn-success btn-lg">
                        <i class="fas fa-play"></i> Join Test Now
                    </a>
                </div>
                @elseif($currentTest && $currentTest->isWaiting())
                <div class="d-grid">
                    @if($isReady)
                        <button class="btn btn-success btn-lg" disabled>
                            <i class="fas fa-check"></i> You're Ready! Waiting for First Question...
                        </button>
                    @else
                        <button class="btn btn-warning btn-lg" onclick="markAsReady()">
                            <i class="fas fa-hand-paper"></i> I'm Ready to Participate
                        </button>
                    @endif
                    <small class="text-muted mt-2">The page will refresh automatically when questions start</small>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Auto-refresh script for real-time updates -->
<script>
    // Refresh more frequently when waiting for test to start
    @if($currentTest && $currentTest->isWaiting())
        setInterval(function() {
            location.reload();
        }, 5000); // Refresh every 5 seconds when waiting
    @else
        setInterval(function() {
            location.reload();
        }, 30000); // Refresh every 30 seconds otherwise
    @endif

        function markAsReady() {
        if (!confirm('Are you ready to participate in this test? Make sure to stay on this page until the test ends.')) {
            return;
        }

        console.log('Marking as ready...');
        console.log('CSRF Token:', '{{ csrf_token() }}');
        console.log('Route:', '{{ route("quiz.mark-ready") }}');

        fetch('{{ route("quiz.mark-ready") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert(data.error || 'Something went wrong. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Something went wrong. Please try again.');
        });
    }
</script>
@endsection