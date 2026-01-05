@extends('layouts.app')

@section('title', 'Quiz Dashboard - University Competition')

@push('scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="row justify-content-center">
<div class="col-md-8 col-lg-6">
<!-- Indicator for active exam - shows when user should return to exam -->
<div id="active-exam-indicator" class="alert alert-warning d-none">
<i class="fas fa-exclamation-triangle"></i>
<strong>Exam in Progress!</strong>
<p class="mb-2">A question is currently active. Please check below.</p>
<button class="btn btn-warning" onclick="scrollToQuestion()">
<i class="fas fa-arrow-down"></i> Go to Question
</button>
</div>

<!-- حاوية حالة الانتظار - تظهر عندما لا يوجد اختبار نشط -->
<div id="waiting-container"
class="card shadow {{ $currentTest && $currentTest->isActive() && $currentTest->isUserReady($user->id) && !$existingAnswer ? '' : ($currentTest && $currentTest->isActive() && $currentTest->isUserReady($user->id) && $existingAnswer ? 'd-none' : '') }}">
<div class="card-header bg-info text-white text-center">
<h4><i class="fas fa-graduation-cap"></i> Welcome to University Competition</h4>
</div>
<div class="card-body text-center">
{{-- إذا كان الاختبار في حالة الانتظار --}}
@if ($currentTest && $currentTest->isWaiting())
<div class="mb-4">
<i class="fas fa-hourglass-half fa-3x text-warning mb-3"></i>
<h3>Test is Ready!</h3>
<p class="lead">The exam manager has prepared the test. {{ $readyCount }} participants are
ready.</p>
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
{{-- إذا كان الاختبار نشطاً والمستخدم جاهز --}}
@elseif($currentTest && $currentTest->isActive() && $currentTest->isUserReady($user->id))

{{-- لا يوجد اختبار حالياً --}}
@else
<div class="mb-4">
<i class="fas fa-clock fa-3x text-muted mb-3"></i>
<h3>Please wait for the test to start</h3>
<p class="text-muted">No test is currently in progress.</p>
</div>
@endif

{{-- قسم معلومات المشارك وحالة الاختبار --}}
<div class="row mt-4">
{{-- بطاقة معلومات المشارك --}}
<div class="col-md-6">
<div class="card bg-light">
<div class="card-body">
<h5><i class="fas fa-user"></i> Participant</h5>
<p class="mb-1"><strong>Name:</strong> {{ $user->name }}</p>
@if ($user->university)
<p class="mb-1"><strong>University:</strong> {{ $user->university }}</p>
@endif
<p class="mb-0"><strong>Role:</strong>
{{ ucfirst(str_replace('_', ' ', $user->role)) }}</p>
</div>
</div>
</div>
{{-- بطاقة حالة الاختبار --}}
<div class="col-md-6">
<div class="card bg-light">
<div class="card-body" id="test-status-content">
<h5><i class="fas fa-info-circle"></i> Test Status</h5>
@if ($currentTest)
{{-- حالة الانتظار --}}
@if ($currentTest->isWaiting())
<p class="mb-1"><span class="badge bg-warning">Waiting</span></p>
<p class="text-muted mb-0">Test is prepared and waiting to start</p>
<p class="mt-2"><i class="fas fa-users"></i> <span id="ready-count-display">{{ $readyCount }}</span> participants ready</p>
@if ($isReady)
<button class="btn btn-sm btn-success mt-2" disabled>
<i class="fas fa-check"></i> I'm Ready!
</button>
@else
<button class="btn btn-sm btn-warning mt-2" onclick="markAsReady()">
<i class="fas fa-hand-paper"></i> I'm Ready!
</button>
@endif
{{-- حالة الاختبار النشط --}}
@elseif($currentTest->isActive())
<p class="mb-1"><span class="badge bg-success">Active</span></p>
<p class="text-muted mb-0">Test is currently in progress</p>
@if ($currentTest->isUserReady($user->id))
<p class="text-success mt-2"><i class="fas fa-check-circle"></i> You are participating</p>
@else
<p class="text-warning mt-2"><i class="fas fa-exclamation-triangle"></i> You missed the start!</p>
@endif
{{-- حالة انتهاء الاختبار --}}
@elseif($currentTest->isEnded())
<p class="mb-1"><span class="badge bg-secondary">Ended</span></p>
<p class="text-muted mb-0">Test has been completed</p>
<a href="{{ route('scoreboard') }}" class="btn btn-sm btn-success mt-2">
<i class="fas fa-trophy"></i> View Results
</a>
@endif
@else
{{-- لا يوجد اختبار مجدول --}}
<p class="mb-1"><span class="badge bg-secondary">No Test</span></p>
<p class="text-muted mb-0">No test is currently scheduled</p>
@endif
</div>
</div>
</div>
</div>

{{-- زر الاستعداد للمشاركة --}}
<div id="join-test-container" class="d-grid mt-4 d-none">

@if ($isReady)
<button class="btn btn-success btn-lg" disabled>
<i class="fas fa-check"></i> You're Ready! Waiting for First Question...
</button>
@else
<button class="btn btn-warning btn-lg" onclick="markAsReady()">
<i class="fas fa-hand-paper"></i> I'm Ready to Participate
</button>
@endif
<small class="text-muted mt-2">The question will appear automatically when the exam manager
starts it</small>
</div>

</div>
</div>

<!-- حاوية انتظار السؤال التالي - تظهر بعد إرسال الإجابة -->
<div id="waiting-for-next-container" class="card shadow d-none">
<div class="card-header bg-success text-white text-center">
<h4><i class="fas fa-check-circle"></i> Answer Submitted!</h4>
</div>
<div class="card-body text-center">
<i class="fas fa-hourglass-half fa-4x text-success mb-4"></i>
<h3>You submitted your answer successfully</h3>
<p class="lead">Wait for the next question...</p>
</div>
</div>

<!-- حاوية السؤال - تظهر عندما يكون الاختبار نشطاً والمستخدم جاهز -->
<div id="question-container" class="{{ ($currentTest && $currentTest->isActive() && $isReady && $question) ? '' : 'd-none' }}">
<div class="card question-card">
{{-- رأس بطاقة السؤال مع المؤقت --}}
<div class="card-header bg-primary text-white">
<div class="d-flex justify-content-between align-items-center">
<h5 class="mb-0"><i class="fas fa-question-circle"></i> Question</h5>
<div class="timer-display" id="timer">35s</div>
</div>
</div>
<div class="card-body">
{{-- نص السؤال --}}
<div class="question-content mb-4">
<h4>{{ $question ? $question->title : 'Loading question...' }}</h4>
</div>

{{-- خيارات الإجابة --}}
<div class="answer-options">
<form id="answerForm">
@csrf
<div class="row">
{{-- الخيار أ --}}
<div class="col-md-6">
<div class="answer-option" data-answer="A">
<div class="d-flex align-items-center">
<span class="badge bg-primary me-3" style="font-size: 1.2rem;">A</span>
<span id="option-a">{{ $question ? $question->option_a : '...' }}</span>
</div>
</div>
</div>
{{-- الخيار ب --}}
<div class="col-md-6">
<div class="answer-option" data-answer="B">
<div class="d-flex align-items-center">
<span class="badge bg-primary me-3" style="font-size: 1.2rem;">B</span>
<span id="option-b">{{ $question ? $question->option_b : '...' }}</span>
</div>
</div>
</div>
</div>
<div class="row mt-3">
{{-- الخيار ج --}}
<div class="col-md-6">
<div class="answer-option" data-answer="C">
<div class="d-flex align-items-center">
<span class="badge bg-primary me-3" style="font-size: 1.2rem;">C</span>
<span id="option-c">{{ $question ? $question->option_c : '...' }}</span>
</div>
</div>
</div>
{{-- الخيار د --}}
<div class="col-md-6">
<div class="answer-option" data-answer="D">
<div class="d-flex align-items-center">
<span class="badge bg-primary me-3" style="font-size: 1.2rem;">D</span>
<span id="option-d" >{{ $question ? $question->option_d : '...' }}</span>
</div>
</div>
</div>
</div>
</form>
</div>

{{-- رسالة الحالة --}}
<div class="text-center mt-4">
<div id="statusMessage"></div>
</div>

{{-- زر إرسال الإجابة --}}
<div class="text-center mt-4">
<button type="button" id="submitAnswerBtn" class="btn btn-success btn-lg"
onclick="submitAnswer()">
<i class="fas fa-check"></i> Submit Answer
</button>
</div>
</div>
</div>

{{-- حقول مخفية لتخزين بيانات السؤال والاختبار --}}
<input type="hidden" id="questionId" value="{{ $question ? $question->id : '' }}">
<input type="hidden" id="testId" value="{{ $currentTest ? $currentTest->id : '' }}">
<input type="hidden" id="startTime"
value="{{ $currentTest ? $currentTest->question_start_time : '' }}">
<input type="hidden" id="timeLimit" value="35">
<input type="hidden" id="hasAnswered" value="{{ $existingAnswer ? 'true' : 'false' }}">
<input type="hidden" id="currentQuestionId" value="{{ $question ? $question->id : '' }}">
</div>
</div>
</div>

<script>
// Global variables for timer and state control
let timeRemaining = 35;
let timerInterval = null;
let hasAnswered = {{ $existingAnswer ? 'true' : 'false' }};
let selectedAnswer = null;
let currentQuestionId = {{ $question ? $question->id : 'null' }};
let pusherConnected = false;

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing quiz dashboard...');
    
    // Check Pusher connection status
    if (window.Echo && window.Echo.connector && window.Echo.connector.pusher) {
        console.log('Echo found, checking connection state...');
        console.log('Current Pusher state:', window.Echo.connector.pusher.connection.state);
        
        // Bind to connection events
        window.Echo.connector.pusher.connection.bind('connected', function() {
            console.log('✓ Pusher connected successfully!');
            console.log('Socket ID:', window.Echo.connector.pusher.connection.socket_id);
            pusherConnected = true;
            attachPusherListeners();
        });
        
        window.Echo.connector.pusher.connection.bind('disconnected', function() {
            console.log('Pusher disconnected');
            pusherConnected = false;
        });
        
        window.Echo.connector.pusher.connection.bind('error', function(err) {
            console.error('Pusher connection error:', err);
        });
        
        // Check current state and attach if already connected
        const currentState = window.Echo.connector.pusher.connection.state;
        console.log('Initial connection state:', currentState);
        
        if (currentState === 'connected') {
            console.log('Already connected, attaching listeners...');
            pusherConnected = true;
            attachPusherListeners();
        } else {
            console.log('Waiting for connection... Current state:', currentState);
        }
    } else {
        console.error('Echo or Pusher not found!');
        console.log('window.Echo:', window.Echo);
    }
    
    // Attach answer option click handlers
    attachAnswerOptionListeners();
    
    // Initialize timer if there's already an active question
    @if ($currentTest && $currentTest->isActive() && $question && !$existingAnswer)
        initializeTimer();
    @endif
    
    // Ensure proper initial state for waiting test
    @if ($currentTest && $currentTest->isWaiting())
        console.log('Test is in waiting status - ready button should be active');
        ensureReadyButtonState();
    @endif
});

/**
 * Attach Pusher event listeners
 */
function attachPusherListeners() {
    if (!window.Echo) {
        console.error('Echo not available');
        return;
    }
    
    console.log('Subscribing to quiz-participants channel...');
    
    const channel = Echo.channel('quiz-participants');
    
    // Global event catcher - logs ALL events
    channel.subscribed(function() {
        console.log('✓ Successfully subscribed to quiz-participants channel');
        console.log('Channel object:', channel);
    }).error(function(error) {
        console.error('Error subscribing to channel:', error);
    });
    
    // Listen for ALL events (catch-all)
    channel.listen('*', function(e) {
        console.log('🎯 EVENT RECEIVED (catch-all):', e);
        console.log('Event name:', e.event);
        console.log('Event data:', e.data);
    });
    
    // Specific event listeners
    channel.listen('.test.started', function(e) {
        console.log('✅ Test started event received:', e);
        handleTestStarted(e);
    });
    
    channel.listen('.question.started', function(e) {
        console.log('✅ Question started event received:', e);
        handleQuestionStarted(e);
    });
    
    channel.listen('.test.ended', function(e) {
        console.log('✅ Test ended event received:', e);
        handleTestEnded(e);
    });
     // Listen for participant.ready event
    channel.listen('.participant.ready', function(e) {
        console.log('✅ Participant ready event received:', e);
        handleParticipantReady(e);
    });
    console.log('✓ Now listening for events on quiz-participants channel');
}

/**
 * Ensure the ready button is in the correct state
 */
function ensureReadyButtonState() {
    const readyButtons = document.querySelectorAll('[onclick="markAsReady()"]');
    readyButtons.forEach(btn => {
        @if (!$isReady)
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-hand-paper"></i> I\'m Ready to Participate';
        btn.classList.add('btn-warning');
        btn.classList.remove('btn-success', 'disabled');
        @endif
    });
}

/**
 * Handle test started event
 */
function handleTestStarted(event) {
    showNotification('Test is Ready! Waiting for participants...');

    // إظهار زر المشاركة
    const joinContainer = document.getElementById('join-test-container');
    if (joinContainer) {
        joinContainer.classList.remove('d-none');
    }

    // إظهار حاوية الانتظار
    const waitingContainer = document.getElementById('waiting-container');
    if (waitingContainer) {
        waitingContainer.classList.remove('d-none');
    }

    updateTestStatusToWaiting();
    setTimeout(hideNotification, 3000);
}

/**
 * Handle participant ready event
 */
function handleParticipantReady(event) {
    console.log('Participant ready:', event);
    
    // Update the ready count display
    const readyCountElement = document.getElementById('ready-count-display');
    if (readyCountElement && event.ready_count !== undefined) {
        readyCountElement.textContent = event.ready_count;
    }
    
    // Update ready count in the main waiting message if exists
    const waitingMessageCount = document.querySelector('.lead');
    if (waitingMessageCount && event.ready_count !== undefined) {
        // Find and update the count in the text
        const text = waitingMessageCount.textContent;
        const countMatch = text.match(/\d+\s+participants/);
        if (countMatch) {
            waitingMessageCount.textContent = text.replace(/\d+\s+participants/, event.ready_count + ' participants');
        }
    }
    
    // Show notification about new participant
    if (event.user_name) {
        showNotification(event.user_name + ' is ready to participate!');
        setTimeout(hideNotification, 3000);
    }
}
/**
 * Handle question started event (improved)
 */
function handleQuestionStarted(event) {
    showNotification('New question received!');

    const question = event.question || event.data?.question;
    const questionStartTime = event.question_start_time || event.data?.question_start_time || Math.floor(Date.now()/1000);
    const timeLimit = event.time_limit || event.data?.time_limit || 35;

    if (!question) {
        console.error('No question data in event');
        return;
    }

    // Hide waiting containers
    document.getElementById('waiting-container')?.classList.add('d-none');
    document.getElementById('waiting-for-next-container')?.classList.add('d-none');

    // Update question container
    updateQuestionContainer(question, questionStartTime, timeLimit);

    // Update test status section
    updateTestStatusToActive();

    setTimeout(hideNotification, 3000);
}

/**
 * Handle test ended event
 */
function handleTestEnded(event) {
    showNotification('Test has ended!');
    
    // Clear timer
    if (timerInterval) {
        clearInterval(timerInterval);
    }
    
    // Hide question container
    document.getElementById('question-container')?.classList.add('d-none');


       // Hide waiting-for-next container (Answer Submitted form)
    document.getElementById('waiting-for-next-container')?.classList.add('d-none');
    
    
    // Show waiting container with ended status
    const waitingContainer = document.getElementById('waiting-container');
    if (waitingContainer) {
        waitingContainer.classList.remove('d-none');
        const cardBody = waitingContainer.querySelector('.card-body');
        if (cardBody) {
            const redirectUrl = event.redirect_url || event.data?.redirect_url || '/scoreboard';
            cardBody.innerHTML = `
                <div class="mb-4">
                    <i class="fas fa-trophy fa-3x text-success mb-3"></i>
                    <h3>Test Completed!</h3>
                    <p class="lead">Thank you for participating.</p>
                    <a href="${redirectUrl}" class="btn btn-success btn-lg">
                        <i class="fas fa-trophy"></i> View Results
                    </a>
                </div>
            `;
        }
    }
    
    setTimeout(hideNotification, 3000);
}

/**
 * Show notification to user
 */
function showNotification(message) {
    const notification = document.getElementById('update-notification');
    const notificationMessage = document.getElementById('notification-message');
    
    if (notification && notificationMessage) {
        notificationMessage.textContent = message;
        notification.classList.remove('d-none');
    }
}

/**
 * Hide notification
 */
function hideNotification() {
    const notification = document.getElementById('update-notification');
    if (notification) {
        notification.classList.add('d-none');
    }
}

/**
 * Update question container with new question data
 */
function updateQuestionContainer(question, questionStartTime, timeLimit) {
    const questionContainer = document.getElementById('question-container');
    if (!questionContainer) return;

    // Show container
    questionContainer.classList.remove('d-none');

       // Clear any previous status messages
    const statusMessage = document.getElementById('statusMessage');
    if (statusMessage) {
        statusMessage.innerHTML = '';
    }

    // Update question text
    const questionTitle = questionContainer.querySelector('.question-content h4');
    if (questionTitle) questionTitle.textContent = question.title;

    // Update options
    const optionElements = {
        A: questionContainer.querySelector('#option-a'),
        B: questionContainer.querySelector('#option-b'),
        C: questionContainer.querySelector('#option-c'),
        D: questionContainer.querySelector('#option-d')
    };
    if (optionElements.A) optionElements.A.textContent = question.option_a;
    if (optionElements.B) optionElements.B.textContent = question.option_b;
    if (optionElements.C) optionElements.C.textContent = question.option_c;
    if (optionElements.D) optionElements.D.textContent = question.option_d;

    // Update hidden inputs
    const questionIdInput = document.getElementById('questionId');
    const currentQuestionIdInput = document.getElementById('currentQuestionId');
    const startTimeInput = document.getElementById('startTime');
    const timeLimitInput = document.getElementById('timeLimit');
    const hasAnsweredInput = document.getElementById('hasAnswered');

    if (questionIdInput) questionIdInput.value = question.id;
    if (currentQuestionIdInput) currentQuestionIdInput.value = question.id;
    if (startTimeInput) startTimeInput.value = questionStartTime;
    if (timeLimitInput) timeLimitInput.value = timeLimit;
    if (hasAnsweredInput) hasAnsweredInput.value = 'false';

    // Reset local state
    hasAnswered = false;
    selectedAnswer = null;
    currentQuestionId = question.id;
    timeRemaining = timeLimit;

    // Reset option styles
    const optionDivs = questionContainer.querySelectorAll('.answer-option');
    optionDivs.forEach(opt => {
        opt.classList.remove('selected');
        opt.style.pointerEvents = 'auto';
        opt.style.opacity = '1';
    });

    // Enable submit button
    const submitBtn = document.getElementById('submitAnswerBtn');
    if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-check"></i> Submit Answer';
    }

    // Initialize timer
    initializeTimer();

    // Re-attach option click listeners
    attachAnswerOptionListeners();
}
/**
 * Update test status section to waiting
 */
function updateTestStatusToWaiting() {
    const statusContent = document.getElementById('test-status-content');
    if (!statusContent) return;
    
    statusContent.innerHTML = `
        <p class="mb-1"><span class="badge bg-warning">Waiting</span></p>
        <p class="text-muted mb-0">Test is prepared and waiting to start</p>
    `;
}

/**
 * Update test status section to active
 */
function updateTestStatusToActive() {
    const statusContent = document.getElementById('test-status-content');
    if (!statusContent) return;
    
    statusContent.innerHTML = `
        <p class="mb-1"><span class="badge bg-success">Active</span></p>
        <p class="text-muted mb-0">Test is currently in progress</p>
        <p class="text-success mt-2"><i class="fas fa-check-circle"></i> You are participating</p>
    `;
}

/**
 * Mark user as ready to participate
 */
function markAsReady() {
    const btn = event.target;
    if (btn.disabled) return;
    
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Processing...';
    
    fetch('/quiz/mark-ready', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('You are ready to participate! Waiting for the first question...');
            
            // Update button state
            btn.innerHTML = '<i class="fas fa-check"></i> You\'re Ready! Waiting for First Question...';
            btn.classList.remove('btn-warning');
            btn.classList.add('btn-success');
            
            // Update ready count display if exists
            const readyCountElement = document.getElementById('ready-count-display');
            if (readyCountElement && data.readyCount !== undefined) {
                readyCountElement.textContent = data.readyCount;
            }
            
            // Update the test status section to show user is ready
            updateReadyStatusDisplay();
        } else {
            showNotification(data.error || 'Error marking as ready');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-hand-paper"></i> I\'m Ready to Participate';
        }
    })
    .catch(error => {
        console.error('Error marking as ready:', error);
        showNotification('An error occurred. Please try again.');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-hand-paper"></i> I\'m Ready to Participate';
    });
}

/**
 * Update the ready status display in the test status section
 */
function updateReadyStatusDisplay() {
    // Find and update any ready status indicators
    const readyBadges = document.querySelectorAll('.ready-badge');
    readyBadges.forEach(badge => {
        badge.textContent = 'Ready';
        badge.classList.remove('bg-warning');
        badge.classList.add('bg-success');
    });
}

/**
 * Submit the selected answer
 */
function submitAnswer() {
    if (!selectedAnswer) {
        showNotification('Please select an answer first!');
        return;
    }
    
    const questionId = document.getElementById('questionId')?.value;
    const submitBtn = document.getElementById('submitAnswerBtn');
    
    if (!questionId) {
        showNotification('No question found!');
        return;
    }
    
    // Disable button to prevent double submission
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Submitting...';
    
    fetch('/quiz/answer', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            selected_answer: selectedAnswer,
            question_id: questionId
        })
    })
     .then(response => response.json())
    .then(data => {
        if (data.success) {
            // عرض رسالة النجاح في منطقة الرسائل
            const statusMessage = document.getElementById('statusMessage');
            if (statusMessage) {
                statusMessage.innerHTML = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Answer submitted successfully!</div>';
            }
            
            
            // Mark as answered locally
            hasAnswered = true;
            const hasAnsweredInput = document.getElementById('hasAnswered');
            if (hasAnsweredInput) {
                hasAnsweredInput.value = 'true';
            }
            
            // Clear timer
            if (timerInterval) {
                clearInterval(timerInterval);
            }
            
                  // Hide question container and show waiting container
            document.getElementById('question-container')?.classList.add('d-none');
            document.getElementById('waiting-for-next-container')?.classList.remove('d-none');
            
          
        } else {
            showNotification(data.error || 'Error submitting answer');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-check"></i> Submit Answer';
        }
    })
    .catch(error => {
        console.error('Error submitting answer:', error);
        showNotification('An error occurred. Please try again.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-check"></i> Submit Answer';
    });
}

/**
 * Initialize the countdown timer
 */
function initializeTimer() {
    const timerElement = document.getElementById('timer');
    const startTimeInput = document.getElementById('startTime');
    const timeLimitInput = document.getElementById('timeLimit');
    
    if (!timerElement) return;
    
    let timeLimit = parseInt(timeLimitInput?.value) || 35;
    let startTime = parseInt(startTimeInput?.value);
    
    // Calculate remaining time based on start time
    if (startTime) {
        const elapsed = Math.floor((Date.now() / 1000) - startTime);
        timeRemaining = Math.max(0, timeLimit - elapsed);
    } else {
        timeRemaining = timeLimit;
    }
    
    // Update timer display
    timerElement.textContent = timeRemaining + 's';
    
    // Clear any existing timer
    if (timerInterval) {
        clearInterval(timerInterval);
    }
    
    // Start countdown
    timerInterval = setInterval(() => {
        timeRemaining--;
        timerElement.textContent = timeRemaining + 's';
        
        // Change color when time is low
        if (timeRemaining <= 10) {
            timerElement.style.color = '#dc3545'; // Red
        } else if (timeRemaining <= 20) {
            timerElement.style.color = '#ffc107'; // Yellow
        }
        
        if (timeRemaining <= 0) {
            clearInterval(timerInterval);
            timerElement.textContent = '0s';
            handleTimeUp();
        }
    }, 1000);
}

/**
 * Handle timer running out
 */
function handleTimeUp() {
    // عرض رسالة انتهاء الوقت الصحيحة
    const statusMessage = document.getElementById('statusMessage');
    if (statusMessage) {
        statusMessage.innerHTML = '<div class="alert alert-danger"><i class="fas fa-times-circle"></i> Time\'s up! You can\'t answer anymore</div>';
    }
    
    // Disable answer options
    const options = document.querySelectorAll('.answer-option');
    options.forEach(option => {
        option.style.pointerEvents = 'none';
        option.style.opacity = '0.6';
    });
    
    // Disable submit button
    const submitBtn = document.getElementById('submitAnswerBtn');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-clock"></i> Time\'s Up';
    }
    
 
    // إذا لم يتم إرسال إجابة، نعرض رسالة انتهاء الوقت بدلاً من "Answer Submitted!"
    if (!selectedAnswer && !hasAnswered) {
        // لا نُظهر حاوية "waiting-for-next-container" هنا
        // لأن المستخدم لم يُرسل إجابة
        console.log('Time expired without answer submission');
    }
}
/**
 * Attach click event listeners to answer options
 */
function attachAnswerOptionListeners() {
    const options = document.querySelectorAll('.answer-option');
    
    options.forEach(option => {
        option.onclick = function() {
            if (hasAnswered) return;
            
            // Remove selected class from all options
            options.forEach(opt => opt.classList.remove('selected'));
            
            // Add selected class to clicked option
            this.classList.add('selected');
            
            // Store selected answer
            selectedAnswer = this.dataset.answer;
            
            // Enable submit button
            const submitBtn = document.getElementById('submitAnswerBtn');
            if (submitBtn) {
                submitBtn.disabled = false;
            }
        };
    });
}

/**
 * Scroll to question section
 */
function scrollToQuestion() {
    const questionContainer = document.getElementById('question-container');
    if (questionContainer) {
        questionContainer.scrollIntoView({ behavior: 'smooth' });
    }
}
</script>
@endsection