@extends('layouts.app')

@section('title', 'Quiz Dashboard - University Competition')

@push('scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <!-- Waiting State Container -->
        <div id="waiting-container" class="card shadow {{ $currentTest && $currentTest->isActive() && $currentTest->isUserReady($user->id) ? 'd-none' : '' }}">
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
                            <li>Stay on this page - the question will appear automatically</li>
                            <li>Keep your browser open</li>
                            <li>The first question will appear when the exam manager starts it</li>
                            <li>Be prepared to answer quickly (35 seconds per question)</li>
                        </ul>
                    </div>
                </div>
                @elseif($currentTest && $currentTest->isActive())
                <div class="mb-4">
                    <i class="fas fa-exclamation-circle fa-3x text-primary mb-3"></i>
                    <h3>Test is in Progress!</h3>
                    <p class="lead">The exam has started. Please wait for the next question.</p>
                    <div class="spinner-border text-primary mt-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                @else
                <div class="mb-4">
                    <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                    <h3>Please wait for the test to start</h3>
                    <p class="text-muted">No test is currently in progress.</p>
                </div>
                @endif

                <div class="row mt-4">
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
                                        @if($currentTest->isUserReady($user->id))
                                            <p class="text-success mt-2"><i class="fas fa-check-circle"></i> You are participating</p>
                                        @else
                                            <p class="text-warning mt-2"><i class="fas fa-exclamation-triangle"></i> You missed the start!</p>
                                        @endif
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

                @if($currentTest && $currentTest->isWaiting())
                <div class="d-grid mt-4">
                    @if($isReady)
                        <button class="btn btn-success btn-lg" disabled>
                            <i class="fas fa-check"></i> You're Ready! Waiting for First Question...
                        </button>
                    @else
                        <button class="btn btn-warning btn-lg" onclick="markAsReady()">
                            <i class="fas fa-hand-paper"></i> I'm Ready to Participate
                        </button>
                    @endif
                    <small class="text-muted mt-2">The question will appear automatically when the exam manager starts it</small>
                </div>
                @endif
            </div>
        </div>

        <!-- Question Container (Shows when test is active and user is ready) -->
        <div id="question-container" class="{{ $currentTest && $currentTest->isActive() && $currentTest->isUserReady($user->id) ? '' : 'd-none' }}">
            <div class="card question-card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-question-circle"></i> Question</h5>
                        <div class="timer-display" id="timer">35s</div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="question-content mb-4">
                        <h4>{{ $question ? $question->title : 'Loading question...' }}</h4>
                    </div>

                    <div class="answer-options">
                        <form id="answerForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="answer-option" data-answer="A">
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-primary me-3" style="font-size: 1.2rem;">A</span>
                                            <span>{{ $question ? $question->option_a : '...' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="answer-option" data-answer="B">
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-primary me-3" style="font-size: 1.2rem;">B</span>
                                            <span>{{ $question ? $question->option_b : '...' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="answer-option" data-answer="C">
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-primary me-3" style="font-size: 1.2rem;">C</span>
                                            <span>{{ $question ? $question->option_c : '...' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="answer-option" data-answer="D">
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-primary me-3" style="font-size: 1.2rem;">D</span>
                                            <span>{{ $question ? $question->option_d : '...' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="text-center mt-4">
                        <div id="statusMessage"></div>
                    </div>

                    @if($existingAnswer)
                    <div class="mt-3">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            You have already answered this question: <strong>{{ $existingAnswer->selected_answer }}</strong>
                            @if($existingAnswer->is_correct)
                                <span class="badge bg-success ms-2">Correct!</span>
                            @else
                                <span class="badge bg-danger ms-2">Incorrect</span>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <input type="hidden" id="questionId" value="{{ $question ? $question->id : '' }}">
            <input type="hidden" id="testId" value="{{ $currentTest ? $currentTest->id : '' }}">
            <input type="hidden" id="startTime" value="{{ $currentTest ? $currentTest->question_start_time : '' }}">
            <input type="hidden" id="timeLimit" value="35">
            <input type="hidden" id="hasAnswered" value="{{ $existingAnswer ? 'true' : 'false' }}">
        </div>
    </div>
</div>

<script>
let timeRemaining = 35;
let timerInterval = null;
let hasAnswered = {{ $existingAnswer ? 'true' : 'false' }};

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Check if test is waiting and user is ready - start 1-second refresh
    @if($currentTest && $currentTest->isWaiting() && $isReady)
        // Start 1-second refresh to check for new questions
        startPolling();
    @elseif($currentTest && $currentTest->isActive() && $currentTest->isUserReady($user->id))
        // Question is already active, initialize the timer
        initializeTimer();
        if (hasAnswered) {
            disableAnswers();
        }
    @endif
});

// Start polling with 1-second refresh
function startPolling() {
    console.log('Starting 1-second refresh polling...');
    setInterval(function() {
        location.reload();
    }, 1000); // Refresh every 1 second
}

// Initialize timer based on server time
function initializeTimer() {
    const startTime = parseInt(document.getElementById('startTime').value);
    const timeLimit = parseInt(document.getElementById('timeLimit').value);
    const currentTime = Math.floor(Date.now() / 1000);

    // Calculate actual remaining time from server timestamp
    const elapsed = currentTime - startTime;
    const calculatedRemaining = timeLimit - elapsed;

    // Use the calculated remaining time, but ensure it's valid
    if (calculatedRemaining > 0 && calculatedRemaining <= timeLimit) {
        timeRemaining = calculatedRemaining;
    } else if (calculatedRemaining <= 0) {
        timeRemaining = 0;
    } else {
        // If calculation seems wrong, use the server value
        timeRemaining = parseInt(document.getElementById('timeLimit').value);
    }

    // Ensure timeRemaining doesn't exceed the limit
    if (timeRemaining > timeLimit) {
        timeRemaining = timeLimit;
    }

    // Update the display immediately
    const timerElement = document.getElementById('timer');
    timerElement.textContent = timeRemaining + 's';
    timerElement.className = 'timer-display bg-warning';

    // Start the countdown
    timerInterval = setInterval(function() {
        timeRemaining--;

        if (timeRemaining <= 0) {
            timerElement.textContent = '0s';
            timerElement.className = 'timer-display bg-danger';
            clearInterval(timerInterval);
            disableAnswers();
            return;
        }

        timerElement.textContent = timeRemaining + 's';

        // Change color based on time remaining
        if (timeRemaining <= 5) {
            timerElement.className = 'timer-display bg-danger';
        } else if (timeRemaining <= 10) {
            timerElement.className = 'timer-display bg-warning';
        }
    }, 1000);
}

// Disable answer selection
function disableAnswers() {
    document.querySelectorAll('.answer-option').forEach(option => {
        option.style.pointerEvents = 'none';
        option.classList.add('disabled');
    });
    hasAnswered = true;
    document.getElementById('hasAnswered').value = 'true';
}

// Mark user as ready
function markAsReady() {
    if (!confirm('Are you ready to participate in this test? Make sure to stay on this page until the test ends.')) {
        return;
    }

    console.log('Marking as ready...');

    fetch('{{ route("quiz.mark-ready") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
    })
    .then(response => {
        console.log('Response status:', response.status);
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

// Answer selection click handlers
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.answer-option').forEach(option => {
        option.addEventListener('click', function() {
            if (hasAnswered || timeRemaining <= 0) return;

            // Remove previous selections
            document.querySelectorAll('.answer-option').forEach(opt => {
                opt.classList.remove('selected');
            });

            // Add selection to clicked option
            this.classList.add('selected');

            // Submit answer
            const selectedAnswer = this.dataset.answer;
            submitAnswer(selectedAnswer);
        });
    });
});

// Submit answer function
function submitAnswer(answer) {
    const questionId = document.getElementById('questionId').value;
    const testId = document.getElementById('testId').value;

    const formData = new FormData();
    formData.append('selected_answer', answer);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

    fetch('/quiz/answer', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showStatus('Answer submitted successfully!', 'success');
            hasAnswered = true;
            document.getElementById('hasAnswered').value = 'true';
        } else {
            showStatus(data.error || 'Error submitting answer', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showStatus('Error submitting answer', 'error');
    });
}

// Show status message
function showStatus(message, type) {
    const statusElement = document.getElementById('statusMessage');
    statusElement.innerHTML = `<div class="alert alert-${type === 'success' ? 'success' : 'danger'}">${message}</div>`;
}
</script>
@endsection