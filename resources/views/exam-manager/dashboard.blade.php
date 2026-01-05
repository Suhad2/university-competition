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
                                <h6>Total Users</h6>
                                <h4>{{ $stats['waiting_users'] }}</h4>
                            </div>
                        </div>
                     <div class="col-md-3">
                        <div class="stat-card">
                            <h6>Ready Participants</h6>
                            <h4 id="ready-count">{{ $stats['ready_participants'] }}</h4>
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
                    <div class="col-md-3">
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
                    <div class="col-md-3">
                        @if($currentTest && $currentTest->isWaiting() && !$currentTest->currentQuestion)
                        <form method="POST" action="{{ route('exam-manager.start-first-question') }}">
                            @csrf
                            <button type="submit" class="btn btn-info btn-lg w-100" 
                                    onclick="return confirm('Start the first question for {{ $stats['ready_participants'] }} ready participants?')">
                                <i class="fas fa-rocket"></i> Start First Question
                            </button>
                        </form>
                        @elseif($currentTest && $currentTest->isActive())
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
                    <div class="col-md-3">
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
@if($currentTest && ($currentTest->isActive() || $currentTest->isWaiting()))
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-users"></i> 
                    @if($currentTest->isWaiting())
                        Ready Participants ({{ $stats['ready_participants'] }})
                    @else
                        Participants Status
                    @endif
                </h5>
            </div>
            <div class="card-body">
                @if($stats['ready_participants'] == 0)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        No participants are ready yet. Wait for students to click "I'm Ready".
                    </div>
                @else
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
                            @php
                            $readyParticipants = $currentTest->getReadyParticipants();
                            @endphp
                            @foreach($users as $user)
                                @if(in_array($user->id, $readyParticipants))
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
                                            @if($currentTest->isWaiting())
                                                <span class="badge bg-info">Ready</span>
                                            @else
                                                <span class="badge bg-warning">Waiting</span>
                                            @endif
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
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
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
document.addEventListener('DOMContentLoaded', function () {

    console.log('Exam Manager Dashboard loaded');

    /* ===============================
       Prevent automatic form submit
    =============================== */
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function (e) {
            const submitter = e.submitter;
            if (submitter && submitter.tagName === 'BUTTON') {
                return true; // allow normal submit
            }
        });
    });

    /* ===============================
       🔴 Echo listener (MAIN UPDATE MECHANISM)
       No more setInterval - 100% Pusher based!
    =============================== */
    if (typeof Echo === 'undefined') {
        console.error('❌ Echo is not defined yet');
        return;
    }

    const channel = Echo.channel('quiz-participants');

    // Log successful subscription
    channel.subscribed(function() {
        console.log('✓ Successfully subscribed to quiz-participants channel');
    }).error(function(error) {
        console.error('❌ Channel subscription error:', error);
    });

    // Listen for ALL events (catch-all for debugging)
    channel.listen('*', function(e) {
        console.log('🎯 EVENT RECEIVED:', e.event, e.data);
    });

    // 1. Participant Ready Event
    Echo.channel('quiz-participants')
        .listen('.participant.ready', function (e) {
            console.log('👤 Participant Ready:', e);
            updateReadyCount(e.ready_count);
            addParticipantRowIfNotExists(e);
        });

    // 2. Test Updated Event (MAIN EVENT FOR UPDATES)
    Echo.channel('quiz-participants')
        .listen('.test.updated', function (e) {
            console.log('📊 Test Updated:', e);
            
            // Update ready count
            if (e.stats && e.stats.ready_participants !== undefined) {
                updateReadyCount(e.stats.ready_participants);
            }
            
            // Update answered questions count
            if (e.stats && e.stats.answered_questions !== undefined) {
                updateAnsweredCount(e.stats.answered_questions);
            }
            
            // Update participants table
            if (e.participants && Array.isArray(e.participants)) {
                updateParticipantsTable(e.participants);
            }
            
            // Update current question if changed
            if (e.currentQuestion) {
                updateCurrentQuestion(e.currentQuestion);
            }
        });

    // 3. Answer Received Event
    Echo.channel('quiz-participants')
        .listen('.answer.received', function (e) {
            console.log('✅ Answer Received:', e);
            updateParticipantAnswer(e.userId, e.selectedAnswer);
        });

    // 4. Question Started Event
    Echo.channel('quiz-participants')
        .listen('.question.started', function (e) {
            console.log('❓ Question Started:', e);
            // Reload page to show new question (needed for full page update)
            const hasActiveTest = {{ $currentTest && $currentTest->isActive() ? 'true' : 'false' }};
            if (hasActiveTest && e.question) {
                location.reload();
            }
        });

    // 5. Test Ended Event
    Echo.channel('quiz-participants')
        .listen('.test.ended', function (e) {
            console.log('🏁 Test Ended:', e);
            showTestEndedNotification();
        });

});

/**
 * Update ready count display
 */
function updateReadyCount(count) {
    const counter = document.getElementById('ready-count');
    if (counter) {
        counter.textContent = count;
    }
}

/**
 * Update answered questions count
 */
function updateAnsweredCount(count) {
    const counter = document.getElementById('answered-count');
    if (counter) {
        counter.textContent = count;
    }
}

/**
 * Add participant row if not exists
 */
function addParticipantRowIfNotExists(eventData) {
    const table = document.getElementById('participantsTable');
    if (!table) return;

    // Check if participant already exists
    const existingRows = table.querySelectorAll('tr td:first-child');
    const exists = Array.from(existingRows).some(td => td.textContent === eventData.user_name);
    
    if (!exists) {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${eventData.user_name}</td>
            <td>${eventData.university || 'N/A'}</td>
            <td><span class="badge bg-info">Ready</span></td>
            <td>-</td>
        `;
        table.appendChild(row);
    }
}

/**
 * Update entire participants table
 */
function updateParticipantsTable(participants) {
    const table = document.getElementById('participantsTable');
    if (!table) return;

    const currentTest = {{ $currentTest && $currentTest->isActive() ? 'true' : 'false' }};
    
    table.innerHTML = '';
    
    participants.forEach(user => {
        // Only show ready participants if test is waiting
        if (!currentTest && !user.is_ready) return;
        
        const row = document.createElement('tr');
        
        let statusBadge = '';
        if (user.has_answered) {
            statusBadge = '<span class="badge bg-success">Answered</span>';
        } else if (user.is_ready) {
            statusBadge = currentTest 
                ? '<span class="badge bg-warning">Waiting</span>'
                : '<span class="badge bg-info">Ready</span>';
        } else {
            statusBadge = '<span class="badge bg-secondary">Not Ready</span>';
        }
        
        row.innerHTML = `
            <td>${user.name}</td>
            <td>${user.university || 'N/A'}</td>
            <td>${statusBadge}</td>
            <td>${user.selected_answer || '-'}</td>
        `;
        table.appendChild(row);
    });
}

/**
 * Update single participant's answer
 */
function updateParticipantAnswer(userId, selectedAnswer) {
    const table = document.getElementById('participantsTable');
    if (!table) return;

    const rows = table.querySelectorAll('tr');
    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length >= 4) {
            const statusCell = cells[2];
            const answerCell = cells[3];
            
            // Update status to answered
            if (statusCell.querySelector('.badge.bg-warning')) {
                statusCell.innerHTML = '<span class="badge bg-success">Answered</span>';
                answerCell.textContent = selectedAnswer;
            }
        }
    });
}

/**
 * Update current question display
 */
function updateCurrentQuestion(question) {
    const questionContainer = document.querySelector('.question-display');
    if (!questionContainer) return;

    const title = questionContainer.querySelector('h4');
    if (title) title.textContent = question.title;

    const options = questionContainer.querySelectorAll('.option-display span:last-child');
    if (options.length >= 4) {
        options[0].textContent = question.option_a;
        options[1].textContent = question.option_b;
        options[2].textContent = question.option_c;
        options[3].textContent = question.option_d;
    }
}

/**
 * Show test ended notification
 */
function showTestEndedNotification() {
    // Update UI to show test ended status
    const statusBadges = document.querySelectorAll('.badge');
    statusBadges.forEach(badge => {
        if (badge.textContent === 'Active') {
            badge.textContent = 'Ended';
            badge.classList.remove('bg-success');
            badge.classList.add('bg-secondary');
        }
    });
}
</script>

@endsection