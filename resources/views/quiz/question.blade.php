@extends('layouts.app')

@section('title', 'Quiz - University Competition')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card question-card">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-question-circle"></i> Question</h5>
                    <div class="timer-display" id="timer">
                        {{ $timeRemaining }}s
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="question-content mb-4">
                    <h4>{{ $question->title }}</h4>
                </div>

                <div class="answer-options">
                    <form id="answerForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="answer-option" data-answer="A">
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-primary me-3" style="font-size: 1.2rem;">A</span>
                                        <span>{{ $question->option_a }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="answer-option" data-answer="B">
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-primary me-3" style="font-size: 1.2rem;">B</span>
                                        <span>{{ $question->option_b }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="answer-option" data-answer="C">
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-primary me-3" style="font-size: 1.2rem;">C</span>
                                        <span>{{ $question->option_c }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="answer-option" data-answer="D">
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-primary me-3" style="font-size: 1.2rem;">D</span>
                                        <span>{{ $question->option_d }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
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

                <div class="text-center mt-4">
                    <div id="statusMessage"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="questionId" value="{{ $question->id }}">
<input type="hidden" id="testId" value="{{ $currentTest->id }}">
<input type="hidden" id="startTime" value="{{ $currentTest->question_start_time }}">
<input type="hidden" id="timeLimit" value="30">
<input type="hidden" id="hasAnswered" value="{{ $existingAnswer ? 'true' : 'false' }}">

@section('scripts')
<script>
let timeRemaining = 30;
let timerInterval;
let hasAnswered = document.getElementById('hasAnswered').value === 'true';

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
}

// Initialize timer
function startTimer() {
    initializeTimer();
    updateTimer();
    timerInterval = setInterval(updateTimer, 1000);
}

function updateTimer() {
    const timerElement = document.getElementById('timer');
    
    if (timeRemaining <= 0) {
        timerElement.textContent = '0s';
        timerElement.classList.add('bg-danger');
        clearInterval(timerInterval);
        disableAnswers();
        return;
    }
    
    timerElement.textContent = timeRemaining + 's';
    
    // Change color based on time remaining
    if (timeRemaining <= 5) {
        timerElement.classList.remove('bg-warning');
        timerElement.classList.add('bg-danger');
    } else if (timeRemaining <= 10) {
        timerElement.classList.remove('bg-success');
        timerElement.classList.add('bg-warning');
    }
    
    timeRemaining--;
}

function disableAnswers() {
    document.querySelectorAll('.answer-option').forEach(option => {
        option.style.pointerEvents = 'none';
        option.classList.add('disabled');
    });
}

function enableAnswers() {
    document.querySelectorAll('.answer-option').forEach(option => {
        option.style.pointerEvents = 'auto';
        option.classList.remove('disabled');
    });
}

// Answer selection
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
        } else {
            showStatus(data.error || 'Error submitting answer', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showStatus('Error submitting answer', 'error');
    });
}

function showStatus(message, type) {
    const statusElement = document.getElementById('statusMessage');
    statusElement.innerHTML = `<div class="alert alert-${type === 'success' ? 'success' : 'danger'}">${message}</div>`;
}

// Check if question is still active
function checkQuestionStatus() {
    const startTime = parseInt(document.getElementById('startTime').value);
    const timeLimit = parseInt(document.getElementById('timeLimit').value);
    const currentTime = Math.floor(Date.now() / 1000);
    
    const elapsed = currentTime - startTime;
    const remaining = timeLimit - elapsed;
    
    if (remaining <= 0) {
        window.location.href = '/dashboard';
        return;
    }
    
    // Update timeRemaining to match server time
    if (remaining !== timeRemaining && remaining >= 0 && remaining <= timeLimit) {
        timeRemaining = remaining;
        const timerElement = document.getElementById('timer');
        timerElement.textContent = timeRemaining + 's';
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    startTimer();
    enableAnswers();
    
    // Check status every 5 seconds to sync with server
    setInterval(checkQuestionStatus, 5000);
});
</script>
@endsection