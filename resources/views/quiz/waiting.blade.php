@extends('layouts.app')

@section('title', 'Waiting for Question - University Competition')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow">
            <div class="card-header bg-warning text-dark text-center">
                <h4><i class="fas fa-clock"></i> Waiting for Next Question</h4>
            </div>
            <div class="card-body text-center">
                <div class="mb-4">
                    <i class="fas fa-hourglass-half fa-4x text-muted mb-3"></i>
                    <h4>Please wait...</h4>
                    <p class="text-muted">The next question will appear automatically</p>
                </div>

                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle"></i> Current Status</h6>
                    @if($currentTest)
                        @if($currentTest->isWaiting())
                            <p class="mb-0">Test is prepared and waiting to start</p>
                        @elseif($currentTest->isActive())
                            <p class="mb-0">Current question is being processed. Next question coming soon...</p>
                        @elseif($currentTest->isEnded())
                            <p class="mb-0">Test has ended. Check the scoreboard for results.</p>
                            <div class="mt-2">
                                <a href="{{ route('scoreboard') }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-trophy"></i> View Results
                                </a>
                            </div>
                        @endif
                    @else
                        <p class="mb-0">No test is currently active</p>
                    @endif
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6><i class="fas fa-user"></i> Participant</h6>
                                <p class="mb-1"><strong>{{ $user->name }}</strong></p>
                                @if($user->university)
                                <p class="mb-0 text-muted">{{ $user->university }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6><i class="fas fa-clock"></i> Auto Refresh</h6>
                                <p class="mb-1">Page refreshes every</p>
                                <p class="mb-0"><strong>10 seconds</strong></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Automatically checking for new questions...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-redirect when test becomes active
setInterval(function() {
    fetch('/dashboard')
        .then(response => response.text())
        .then(html => {
            // Check if there's an active question by looking for quiz link
            if (html.includes('/quiz"') || html.includes('href="/quiz"')) {
                window.location.href = '/quiz';
            }
        })
        .catch(error => console.error('Error checking status:', error));
}, 5000); // Check every 5 seconds

// Auto-refresh page every 15 seconds as backup
setTimeout(function() {
    location.reload();
}, 15000);
</script>
@endsection
