<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>University Competition - Live View</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<style>
* {
	margin: 0;
	padding: 0;
	box-sizing: border-box;
}

html,
body {
	height: 100vh;
	overflow: hidden;
	font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.guest-container {
	height: 100vh;
	display: flex;
	flex-direction: column;
	overflow: hidden;
}

/* Section 1: Competition Title */
.competition-header {
	background: rgba(255, 255, 255, 0.95);
	padding: 1rem 2rem;
	text-align: center;
	box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
	flex-shrink: 0;
}

.competition-header h1 {
	font-size: 2.2rem;
	font-weight: 800;
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
	-webkit-background-clip: text;
	-webkit-text-fill-color: transparent;
	background-clip: text;
	margin: 0;
}

.competition-header .subtitle {
	font-size: 0.95rem;
	color: #6c757d;
	margin-top: 0.25rem;
}

/* Section 2: Stats Cards */
.stats-section {
	padding: 1rem 2rem;
	background: rgba(255, 255, 255, 0.1);
	flex-shrink: 0;
}

.stat-card {
	background: rgba(255, 255, 255, 0.95);
	border-radius: 16px;
	padding: 1.25rem;
	text-align: center;
	height: 100%;
	box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
	transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.stat-card:hover {
	transform: translateY(-3px);
	box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.stat-card .icon-wrapper {
	width: 55px;
	height: 55px;
	border-radius: 50%;
	display: flex;
	align-items: center;
	justify-content: center;
	margin: 0 auto 0.75rem;
	font-size: 1.5rem;
}

.stat-card .stat-value {
	font-size: 2rem;
	font-weight: 700;
	color: #1e293b;
	line-height: 1.2;
}

.stat-card .stat-label {
	font-size: 0.8rem;
	text-transform: uppercase;
	letter-spacing: 0.5px;
	color: #64748b;
	margin-top: 0.25rem;
}

.stat-card.primary .icon-wrapper {
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
	color: white;
}

.stat-card.success .icon-wrapper {
	background: linear-gradient(135deg, #10b981 0%, #059669 100%);
	color: white;
}

.stat-card.warning .icon-wrapper {
	background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
	color: white;
}

.stat-card.info .icon-wrapper {
	background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
	color: white;
}

/* Section 3: Main Content */
.main-content {
	flex: 1;
	padding: 1rem 2rem;
	overflow: hidden;
	display: flex;
	flex-direction: column;
}

.content-wrapper {
	height: 100%;
	display: flex;
	gap: 1rem;
	overflow: hidden;
}

.content-wrapper.ended-mode {
	display: block;
}

/* Left Column: Participants Table */
.participants-panel {
	background: rgba(255, 255, 255, 0.95);
	border-radius: 16px;
	box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
	overflow: hidden;
	display: flex;
	flex-direction: column;
	transition: all 0.5s ease;
}

.participants-panel.ended-mode {
	display: none;
}

.panel-header {
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
	color: white;
	padding: 0.85rem 1.25rem;
	font-weight: 600;
	display: flex;
	align-items: center;
	gap: 0.5rem;
}

.panel-body {
	flex: 1;
	overflow-y: auto;
	padding: 0;
}

.participants-table {
	margin: 0;
	font-size: 0.9rem;
}

.participants-table thead th {
	background: #f1f5f9;
	position: sticky;
	top: 0;
	font-weight: 600;
	color: #475569;
	padding: 0.75rem 1rem;
	border-bottom: 2px solid #e2e8f0;
	white-space: nowrap;
}

.participants-table tbody td {
	padding: 0.75rem 1rem;
	vertical-align: middle;
	border-bottom: 1px solid #f1f5f9;
}

.participants-table tbody tr:hover {
	background: #f8fafc;
}

.status-badge {
	padding: 0.35rem 0.75rem;
	border-radius: 20px;
	font-size: 0.75rem;
	font-weight: 600;
	text-transform: uppercase;
	letter-spacing: 0.3px;
}

.status-badge.ready {
	background: #d1fae5;
	color: #065f46;
}

.status-badge.waiting {
	background: #fef3c7;
	color: #92400e;
}

.status-badge.answered {
	background: #dbeafe;
	color: #1e40af;
}

.status-badge.ended {
	background: #e2e8f0;
	color: #475569;
}

/* Right Column: Question Display */
.question-panel {
	background: rgba(255, 255, 255, 0.95);
	border-radius: 16px;
	box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
	overflow: hidden;
	display: flex;
	flex-direction: column;
	transition: all 0.5s ease;
}

.question-panel.ended-mode {
	width: 100% !important;
	max-width: 900px;
	margin: 0 auto;
}

.question-header {
	background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
	color: white;
	padding: 1rem 1.5rem;
	display: flex;
	justify-content: space-between;
	align-items: center;
}

.timer-display {
	font-size: 2rem;
	font-weight: 700;
	font-family: 'Courier New', monospace;
	background: rgba(255, 255, 255, 0.1);
	padding: 0.5rem 1.25rem;
	border-radius: 10px;
	min-width: 100px;
	text-align: center;
}

.timer-display.warning {
	color: #fbbf24;
	animation: pulse 1s infinite;
}

.timer-display.danger {
	color: #ef4444;
	animation: pulse 0.5s infinite;
}

@keyframes pulse {
	0%, 100% { opacity: 1; }
	50% { opacity: 0.6; }
}

.question-body {
	flex: 1;
	padding: 1.5rem;
	overflow-y: auto;
}

.question-number {
	font-size: 0.85rem;
	color: #64748b;
	text-transform: uppercase;
	letter-spacing: 1px;
	margin-bottom: 0.5rem;
}

.question-text {
	font-size: 1.4rem;
	font-weight: 600;
	color: #1e293b;
	line-height: 1.5;
	margin-bottom: 1.5rem;
}

.options-grid {
	display: flex;
	flex-direction: column;
	gap: 0.75rem;
}

.option-item {
	display: flex;
	align-items: center;
	padding: 1rem 1.25rem;
	background: #f8fafc;
	border: 2px solid #e2e8f0;
	border-radius: 12px;
	transition: all 0.3s ease;
	cursor: default;
}

.option-item .option-letter {
	width: 40px;
	height: 40px;
	border-radius: 10px;
	background: #e2e8f0;
	color: #475569;
	display: flex;
	align-items: center;
	justify-content: center;
	font-weight: 700;
	font-size: 1.1rem;
	margin-right: 1rem;
	flex-shrink: 0;
}

.option-item .option-text {
	font-size: 1.1rem;
	color: #334155;
	flex: 1;
}

.option-item.correct {
	background: #d1fae5;
	border-color: #10b981;
}

.option-item.correct .option-letter {
	background: #10b981;
	color: white;
}

.option-item.correct .option-text {
	color: #065f46;
	font-weight: 600;
}

.option-item.incorrect {
	background: #fee2e2;
	border-color: #ef4444;
	opacity: 0.7;
}

/* Waiting State */
.waiting-state {
	display: flex;
	flex-direction: column;
	align-items: center;
	justify-content: center;
	height: 100%;
	text-align: center;
	padding: 2rem;
}

.waiting-state .waiting-icon {
	font-size: 4rem;
	color: #cbd5e1;
	margin-bottom: 1rem;
}

.waiting-state h3 {
	color: #475569;
	font-size: 1.5rem;
	margin-bottom: 0.5rem;
}

.waiting-state p {
	color: #94a3b8;
	font-size: 1rem;
}

/* Scoreboard */
.scoreboard-container {
	display: none;
	height: 100%;
	overflow-y: auto;
}

.scoreboard-container.active {
	display: block;
}

/* Scrollbar Styling */
.panel-body::-webkit-scrollbar {
	width: 8px;
}

.panel-body::-webkit-scrollbar-track {
	background: #f1f5f9;
}

.panel-body::-webkit-scrollbar-thumb {
	background: #cbd5e1;
	border-radius: 4px;
}

.panel-body::-webkit-scrollbar-thumb:hover {
	background: #94a3b8;
}

/* Responsive adjustments */
@media (max-width: 1200px) {
	.competition-header h1 { font-size: 1.8rem; }
	.stat-card .stat-value { font-size: 1.6rem; }
	.question-text { font-size: 1.2rem; }
}

@media (max-width: 768px) {
	.guest-container {
		height: auto;
		min-height: 100vh;
	}
	html, body { overflow: auto; }
	.stats-section { padding: 0.75rem 1rem; }
	.stat-card { margin-bottom: 0.5rem; }
	.main-content { padding: 0.75rem 1rem; }
}

/* Animation for new data */
@keyframes highlight {
	0% { background: #dbeafe; }
	100% { background: transparent; }
}

.highlight-row {
	animation: highlight 2s ease-out;
}

.stat-card .logo-wrapper {
	width: 55px;
	height: 55px;
	border-radius: 50%;
	display: flex;
	align-items: center;
	justify-content: center;
	margin: 0 auto 0.75rem;
	background: rgba(255, 255, 255, 0.9);
	overflow: hidden;
}

.stat-card .stat-logo {
	max-width: 100%;
	max-height: 100%;
	width: auto;
	height: auto;
	object-fit: contain;
}
</style>

<!-- Pusher configuration -->
<meta name="pusher-key" content="17ec3014a90b3757e007">
<meta name="pusher-cluster" content="mt1">
</head>

<body>
<div class="guest-container">
<!-- Part 1: Competition Title -->
<header class="competition-header">
	<h1><i class="fas fa-graduation-cap"></i> The Fifth Annual Academic Debate on English Studies </h1>
</header>

<!-- Part 2: Four Stats Cards -->
<section class="stats-section">
<div class="row g-3">
<div class="col-md-3 col-sm-6">
<div class="stat-card primary">
<div class="logo-wrapper">
<img src="{{ asset('images/4.jpg') }}" alt="Alzahraa University" class="stat-logo">
</div>
<div class="stat-label">Alzahraa Univ.</div>
</div>
</div>
<div class="col-md-3 col-sm-6">
<div class="stat-card success">
<div class="logo-wrapper">
<img src="{{ asset('images/2.jpg') }}" alt="University of Kufa" class="stat-logo">
</div>
<div class="stat-label">Univ. of Kufa</div>
</div>
</div>
<div class="col-md-3 col-sm-6">
<div class="stat-card warning">
<div class="logo-wrapper">
<img src="{{ asset('images/3.jpg') }}" alt="University of Karbala" class="stat-logo">
</div>
<span id="current-question" style="display: none;">-</span>
<div class="stat-label">Univ. of Karbala</div>
</div>
</div>
<div class="col-md-3 col-sm-6">
<div class="stat-card info">
<div class="logo-wrapper">
<img src="{{ asset('images/1.jpg') }}" alt="University of Babylon" class="stat-logo">
</div>
<span id="time-remaining" style="display: none;">--</span>
<div class="stat-label">Univ. of Baghdad </div>
</div>
</div>
</section>

<!-- Part 3: Main Content (Split View) -->
<main class="main-content">
<div class="content-wrapper" id="content-wrapper">
<!-- Left: Participants Status Table -->
<div class="participants-panel" id="participants-panel" style="flex: 0 0 40%;">
<div class="panel-header">
	<i class="fas fa-users"></i>
	<span>Participants Status</span>
	<span class="badge bg-light text-dark ms-auto" id="participant-count-badge">0</span>
</div>
<!-- Participants Status -->
@if ($currentTest && ($currentTest->isActive() || $currentTest->isWaiting()))
<div class="row">
<div class="col-12">
<div class="card">
<div class="card-header">
<h5><i class="fas fa-users"></i>
@if ($currentTest->isWaiting())
Ready Participants ({{ $stats['ready_participants'] }})
@else
Participants Status
@endif
</h5>
</div>
<div class="card-body">
@if ($stats['ready_participants'] == 0)
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
@foreach ($users as $user)
@if (in_array($user->id, $readyParticipants))
<tr>
<td>{{ $user->name }}</td>
<td>{{ $user->university ?? 'N/A' }}</td>
<td>
@php
$hasAnswered = \App\Models\Answer::where(
'test_id',
$currentTest->id,
)
->where('user_id', $user->id)
->where(
'question_id',
$currentTest->current_question_id ??
0,
)
->exists();
@endphp
@if ($hasAnswered)
<span class="badge bg-success">Answered</span>
@else
@if ($currentTest->isWaiting())
<span class="badge bg-info">Ready</span>
@else
<span class="badge bg-warning">Waiting</span>
@endif
@endif
</td>
<td>
@if ($hasAnswered)
@php
$answer = \App\Models\Answer::where(
'test_id',
$currentTest->id,
)
->where('user_id', $user->id)
->where(
'question_id',
$currentTest->current_question_id ??
0,
)
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
</div>

<!-- Right: Question Display -->
<div class="question-panel" id="question-panel" style="flex: 0 0 60%;">
<div class="question-header">
	<span><i class="fas fa-question-circle me-2"></i>Current Question</span>
	<div class="timer-display" id="timer-display">--</div>
</div>
<div class="question-body" id="question-body">
	<!-- Waiting State -->
	<div class="waiting-state" id="waiting-state">
		<div class="waiting-icon">
			<i class="fas fa-hourglass-half"></i>
		</div>
		<h3>Waiting for Next Question</h3>
		<p>The exam manager will start the question soon...</p>
	</div>

	<!-- Question Content (Hidden by default) -->
	<div id="question-content" style="display: none;">
		<div class="question-number">Question <span id="question-number">1</span></div>
		<div class="question-text" id="question-text">Loading question...</div>
		<div class="options-grid" id="options-grid">
			<div class="option-item" data-option="A">
				<span class="option-letter">A</span>
				<span class="option-text" id="option-a">...</span>
			</div>
			<div class="option-item" data-option="B">
				<span class="option-letter">B</span>
				<span class="option-text" id="option-b">...</span>
			</div>
			<div class="option-item" data-option="C">
				<span class="option-letter">C</span>
				<span class="option-text" id="option-c">...</span>
			</div>
			<div class="option-item" data-option="D">
				<span class="option-letter">D</span>
				<span class="option-text" id="option-d">...</span>
			</div>
		</div>
	</div>
</div>
</div>
</div>
</main>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/pusher-js@7.2.0/dist/web/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.3/dist/echo.iife.js"></script>

<script>
// Application State
const state = {
	currentTest: null,
	currentQuestion: null,
	questionStartTime: null,
	timeLimit: 35,
	timeRemaining: 0,
	timerInterval: null,
	isEnded: false,
	participants: @json($participantsData ?? []),
	correctAnswer: null,
	hasTimeExpired: false
};

// LocalStorage key for question persistence
const QUESTION_STATE_KEY = 'guest_question_state';

// Save question state to localStorage
function saveQuestionState() {
	if (state.currentQuestion && !state.isEnded) {
		const questionState = {
			question: state.currentQuestion,
			questionStartTime: state.questionStartTime,
			timeLimit: state.timeLimit,
			timeRemaining: state.timeRemaining,
			correctAnswer: state.correctAnswer,
			hasTimeExpired: state.hasTimeExpired,
			savedAt: Date.now()
		};
		localStorage.setItem(QUESTION_STATE_KEY, JSON.stringify(questionState));
		console.log('Question state saved to localStorage:', questionState);
	}
}

// Restore question state from localStorage
function restoreQuestionState() {
	const savedState = localStorage.getItem(QUESTION_STATE_KEY);
	if (savedState) {
		try {
			const questionState = JSON.parse(savedState);
			console.log('Restoring question state from localStorage:', questionState);

			// Check if the saved state is still valid (not too old)
			const timeSinceSave = Date.now() - (questionState.savedAt || 0);
			const maxAge = 5 * 60 * 1000; // 5 minutes max

			if (timeSinceSave < maxAge && questionState.question) {
				// Restore the state
				state.currentQuestion = questionState.question;
				state.questionStartTime = questionState.questionStartTime;
				state.timeLimit = questionState.timeLimit || 35;
				state.correctAnswer = questionState.correctAnswer;
				state.hasTimeExpired = questionState.hasTimeExpired || false;

				// Calculate remaining time based on elapsed time since save
				const elapsedSinceSave = Math.floor((Date.now() - questionState.savedAt) / 1000);
				state.timeRemaining = Math.max(0, (questionState.timeRemaining || questionState.timeLimit) - elapsedSinceSave);

				// Update UI to show the question
				updateQuestionDisplay(state.currentQuestion);

				// Start timer with restored remaining time
				restoreTimer();

				// If time had already expired when saved, show correct answer
				if (state.hasTimeExpired && state.correctAnswer) {
					setTimeout(() => {
						highlightCorrectAnswer(state.correctAnswer);
					}, 100);
				}

				console.log('Question state restored successfully');
			} else {
				console.log('Saved question state is too old or invalid, clearing...');
				clearQuestionState();
			}
		} catch (e) {
			console.error('Error restoring question state:', e);
			clearQuestionState();
		}
	}
}

// Clear question state from localStorage
function clearQuestionState() {
	localStorage.removeItem(QUESTION_STATE_KEY);
	console.log('Question state cleared from localStorage');
}

// Update question display without starting timer (used when restoring)
function updateQuestionDisplay(question) {
	if (!question) return;

	// Show question content
	elements.waitingState.style.display = 'none';
	elements.questionContent.style.display = 'block';

	// Update question display
	elements.questionNumber.textContent = question.question_number || '1';
	elements.questionText.textContent = question.title;
	elements.currentQuestion.textContent = '#' + (question.question_number || '1');

	// Update options
	document.getElementById('option-a').textContent = question.option_a;
	document.getElementById('option-b').textContent = question.option_b;
	document.getElementById('option-c').textContent = question.option_c;
	document.getElementById('option-d').textContent = question.option_d;
}

// Restore timer with remaining time
function restoreTimer() {
	// Update displays with restored remaining time
	updateTimerDisplay();
	elements.timeRemaining.textContent = state.timeRemaining + 's';

	// Clear existing timer
	if (state.timerInterval) {
		clearInterval(state.timerInterval);
	}

	// Start countdown with restored remaining time
	state.timerInterval = setInterval(() => {
		state.timeRemaining--;
		updateTimerDisplay();
		elements.timeRemaining.textContent = state.timeRemaining + 's';

		// Save state periodically
		saveQuestionState();

		if (state.timeRemaining <= 0) {
			handleTimeUp();
		}
	}, 1000);
}

// Initialize Echo/Pusher
window.Echo = new Echo({
	broadcaster: 'pusher',
	key: document.querySelector('meta[name="pusher-key"]')?.getAttribute('content') || '17ec3014a90b3757e007',
	cluster: document.querySelector('meta[name="pusher-cluster"]')?.getAttribute('content') || 'mt1',
	forceTLS: true,
	enableLogging: true
});

console.log('Echo initialized for guest view');

// DOM Elements
const elements = {
	totalParticipants: document.getElementById('total-participants'),
	readyCount: document.getElementById('ready-count'),
	currentQuestion: document.getElementById('current-question'),
	timeRemaining: document.getElementById('time-remaining'),
	participantCountBadge: document.getElementById('participant-count-badge'),
	participantsTable: document.getElementById('participantsTable'),
	waitingState: document.getElementById('waiting-state'),
	questionContent: document.getElementById('question-content'),
	questionNumber: document.getElementById('question-number'),
	questionText: document.getElementById('question-text'),
	timerDisplay: document.getElementById('timer-display'),
	optionsGrid: document.getElementById('options-grid'),
	scoreboardContainer: document.getElementById('scoreboard-container'),
	participantsPanel: document.getElementById('participants-panel'),
	questionPanel: document.getElementById('question-panel'),
	contentWrapper: document.getElementById('content-wrapper'),
	scoreboardBody: document.getElementById('scoreboard-body')
};

// Initialize application
document.addEventListener('DOMContentLoaded', function() {
	console.log('Guest Landing Page initialized');
	console.log('Initial participants:', state.participants.length);

	// Update table with initial data from Blade template immediately
	if (state.participants.length > 0) {
		updateParticipantsTable(state.participants);
	}

	// Initialize stats from Blade template
	if (elements.participantCountBadge) {
		elements.participantCountBadge.textContent = {{ $stats['ready_participants'] ?? 0 }};
	}

	// Restore question state from localStorage (in case of page refresh)
	restoreQuestionState();

	// Subscribe to Pusher events
	subscribeToChannel();
	// Fetch fresh data from server immediately
	fetchInitialData();

	// Start polling for updates every 2 seconds (triggers TestUpdated event)
	setInterval(fetchInitialData, 2000);
});

// Subscribe to Pusher channel
function subscribeToChannel() {
	const channel = Echo.channel('quiz-participants');

	channel.subscribed(function() {
		console.log('✓ Guest subscribed to quiz-participants channel');
	}).error(function(error) {
		console.error('❌ Channel subscription error:', error);
	});

	// Listen for all events
	channel.listen('*', function(e) {
		console.log('🎯 EVENT RECEIVED:', e.event, e.data);
		handleEvent(e.event, e.data);
	});

	// Specific event handlers
	channel.listen('.test.started', function(e) {
		console.log('✅ Test started:', e);
		handleTestStarted(e);
	});

	channel.listen('.test.updated', function(e) {
		console.log('✅ Test updated:', e);
		handleTestUpdated(e);
	});

	channel.listen('.participant.ready', function(e) {
		console.log('✅ Participant ready:', e);
		handleParticipantReady(e);
	});

	channel.listen('.question.started', function(e) {
		console.log('✅ Question started:', e);
		handleQuestionStarted(e);
	});

	channel.listen('.answer.received', function(e) {
		console.log('✅ Answer received:', e);
		handleAnswerReceived(e);
	});

	channel.listen('.test.ended', function(e) {
		console.log('✅ Test ended:', e);
		handleTestEnded(e);
	});

	// Listen for new test started
	channel.listen('.test.started', function(e) {
		console.log('✅ New test started:', e);
		handleTestStarted(e);
	});
}

// Event handlers
function handleEvent(eventName, data) {
	// Central event handling
}

function handleTestStarted(data) {
	console.log('✅ Test started:', data);

	// Reset ended state
	state.isEnded = false;

	// Stop any running timer
	if (state.timerInterval) {
		clearInterval(state.timerInterval);
		state.timerInterval = null;
	}

	// Reset timer displays
	elements.timerDisplay.textContent = '--';
	elements.timeRemaining.textContent = '--';

	// Reset question state
	state.currentQuestion = null;
	state.questionStartTime = null;
	state.timeRemaining = 0;
	state.hasTimeExpired = false;
	state.correctAnswer = null;

	// Clear localStorage state
	clearQuestionState();

	// Reset UI: Show split panels
	elements.participantsPanel.style.display = 'flex';
	elements.questionPanel.style.display = 'flex';

	// Clear correct answer highlighting
	clearCorrectAnswerHighlighting();

	// Reset to waiting state in question panel
	elements.waitingState.style.display = 'flex';
	elements.questionContent.style.display = 'none';

	// Update stats if available
	if (data.test) {
		state.currentTest = data.test;
		updateStats();
	}
}

function handleTestUpdated(data) {
	console.log('📊 TestUpdated event received:', data);

	// Update participants from event data
	if (data.participants && Array.isArray(data.participants)) {
		state.participants = data.participants;
		updateParticipantsTable(data.participants);
		console.log('✅ Updated ' + data.participants.length + ' participants');
	}

	// Update stats
	if (data.stats) {
		if (elements.participantCountBadge) {
			elements.participantCountBadge.textContent = data.stats.ready_participants || 0;
		}
		if (elements.readyCount) {
			elements.readyCount.textContent = data.stats.ready_participants || 0;
		}
	}
}

function handleParticipantReady(data) {
	console.log('👤 ParticipantReady event:', data);

	if (data.ready_count !== undefined && elements.readyCount) {
		elements.readyCount.textContent = data.ready_count;
	}
	if (data.ready_count !== undefined && elements.participantCountBadge) {
		elements.participantCountBadge.textContent = data.ready_count;
	}

	// Add or update participant in the list
	if (data.user_name) {
		const newParticipant = {
			id: data.user_id,
			name: data.user_name,
			university: data.university || 'N/A',
			status: 'ready',
			has_answered: false,
			selected_answer: null
		};

		// Check if participant already exists
		const existingIndex = state.participants.findIndex(p => p.id === data.user_id);
		if (existingIndex >= 0) {
			// Update existing participant
			state.participants[existingIndex] = newParticipant;
		} else {
			// Add new participant
			state.participants.push(newParticipant);
		}

		// Update the table
		updateParticipantsTable(state.participants);
		console.log('✅ Participant added/updated:', data.user_name);
	}
}

function handleQuestionStarted(data) {
	const question = data.question || data;
	state.currentQuestion = question;
	state.questionStartTime = data.question_start_time || Math.floor(Date.now() / 1000);
	state.timeLimit = data.time_limit || 35;
	state.hasTimeExpired = false;
	state.correctAnswer = null;

	console.log('Question started:', question);
	console.log('Correct answer:', question.correct_answer);

	// Fallback: check if correct_answer is in parent data (from TestUpdated event)
	if (!question.correct_answer && data.correct_answer) {
		question.correct_answer = data.correct_answer;
		console.log('Correct answer from parent:', data.correct_answer);
	}

	// Clear previous correct answer highlighting
	clearCorrectAnswerHighlighting();

	// Update question display
	updateQuestionDisplay(question);

	// Start timer
	startTimer();

	// Save question state to localStorage for persistence on refresh
	saveQuestionState();
}

function handleAnswerReceived(data) {
	console.log('✅ AnswerReceived event:', data);

	// Update participant's answer status in table
	if (data.user_id) {
		const participant = state.participants.find(p => p.id === data.user_id);
		if (participant) {
			participant.has_answered = true;
			participant.selected_answer = data.selected_answer || data.answer;
			participant.status = 'answered';
			updateParticipantsTable(state.participants);
			console.log('✅ Participant marked as answered');
		}
	}
}

function handleTestEnded(data) {
	state.isEnded = true;

	// Stop timer
	if (state.timerInterval) {
		clearInterval(state.timerInterval);
	}

	// Clear timer display
	elements.timerDisplay.textContent = '--';
	elements.timeRemaining.textContent = '--';

	// Clear question state from localStorage
	clearQuestionState();

	// Redirect to existing scoreboard page
	console.log('Test ended, redirecting to scoreboard page...');
	window.location.href = '/scoreboard';
}

// Fetch initial data from server
let consecutiveErrors = 0;
const maxErrorsBeforePause = 3;
let pollingPaused = false;
let retryTimeout = null;
async function fetchInitialData() {
	// Don't poll if we've paused due to errors
	if (pollingPaused) {
		return;
	}

	try {
		// Call polling endpoint which broadcasts TestUpdated event
		const response = await axios.get('/guest/poll');
		consecutiveErrors = 0;
		console.log('Polling successful');

		// If we received data, update the UI
		if (response.data && response.data.participants) {
			state.participants = response.data.participants;
			updateParticipantsTable(response.data.participants);

			// Update stats if available
			if (response.data.stats) {
				if (elements.participantCountBadge) {
					elements.participantCountBadge.textContent = response.data.stats.ready_participants || 0;
				}
			}
		}

		// Check if test has ended and redirect to scoreboard page
		if (response.data.currentTest && response.data.currentTest.is_ended && !state.isEnded) {
			console.log('Test has ended, redirecting to scoreboard page...');
			
			// Set ended state to prevent multiple redirects
			state.isEnded = true;
			
			// Redirect to existing scoreboard page
			window.location.href = '/scoreboard';
			return;
		}
	} catch (error) {
		consecutiveErrors++;
		console.error('Polling error (attempt ' + consecutiveErrors + '):', error.message);

		// Show error in console with more details
		if (error.response) {
			console.error('Server responded with:', error.response.status, error.response.data);
		}

		// Pause polling if too many consecutive errors
		if (consecutiveErrors >= maxErrorsBeforePause) {
			pollingPaused = true;
			console.warn('Too many consecutive polling errors, pausing...');

			// Try to recover after 30 seconds
			retryTimeout = setTimeout(() => {
				console.log('Attempting to resume polling...');
				consecutiveErrors = 0;
				pollingPaused = false;
				fetchInitialData();
			}, 30000);
		}
	}
}

// Update participants table from state
function updateFromState() {
	if (state.participants && Array.isArray(state.participants)) {
		updateParticipantsTable(state.participants);

		// Update stats
		const readyCount = state.participants.filter(p => p.status === 'ready' || p.status === 'waiting' || p.status === 'answered').length;
		if (elements.participantCountBadge) {
			elements.participantCountBadge.textContent = readyCount;
		}
	}
}

// Update participants table
function updateParticipantsTable(participants) {
	state.participants = participants;
	elements.participantCountBadge.textContent = participants.length;

	if (!elements.participantsTable) {
		console.warn('Participants table element not found');
		return;
	}

	let html = '';
	participants.forEach(participant => {
		let statusClass = '';
		let statusText = '';

		if (participant.status === 'ready') {
			statusClass = 'ready';
			statusText = 'Ready';
		} else if (participant.has_answered) {
			statusClass = 'answered';
			statusText = 'Answered';
		} else if (participant.status === 'waiting') {
			statusClass = 'waiting';
			statusText = 'Waiting';
		} else if (participant.status === 'ended') {
			statusClass = 'ended';
			statusText = 'Ended';
		}

		html += `
			<tr class="highlight-row">
				<td>
					<strong>${escapeHtml(participant.name)}</strong>
				</td>
				<td>${escapeHtml(participant.university || 'N/A')}</td>
				<td>
					<span class="status-badge ${statusClass}">${statusText}</span>
				</td>
				<td>
					${participant.selected_answer ? '<strong>' + participant.selected_answer + '</strong>' : '<span class="text-muted">-</span>'}
				</td>
			</tr>
		`;
	});

	elements.participantsTable.innerHTML = html || '<tr><td colspan="4" class="text-center text-muted py-4">No participants yet. Waiting for participants to join...</td></tr>';
}

// Add or update single participant
function addOrUpdateParticipant(participant) {
	const existingIndex = state.participants.findIndex(p => p.id === participant.id);

	if (existingIndex >= 0) {
		state.participants[existingIndex] = participant;
	} else {
		state.participants.push(participant);
	}

	updateParticipantsTable(state.participants);
}

// Update participant answer
function updateParticipantAnswer(userId, selectedAnswer) {
	const participant = state.participants.find(p => p.id === userId);
	if (participant) {
		participant.has_answered = true;
		participant.selected_answer = selectedAnswer;
		updateParticipantsTable(state.participants);
	}
}

// Timer functions
function startTimer() {
	// Calculate remaining time based on start time
	const startTime = state.questionStartTime;
	const elapsed = Math.floor((Date.now() / 1000) - startTime);
	state.timeRemaining = Math.max(0, state.timeLimit - elapsed);

	// Update displays
	updateTimerDisplay();
	elements.timeRemaining.textContent = state.timeRemaining + 's';

	// Clear existing timer
	if (state.timerInterval) {
		clearInterval(state.timerInterval);
	}

	// Start countdown
	state.timerInterval = setInterval(() => {
		state.timeRemaining--;
		updateTimerDisplay();
		elements.timeRemaining.textContent = state.timeRemaining + 's';

		if (state.timeRemaining <= 0) {
			handleTimeUp();
		}
	}, 1000);
}

function updateTimerDisplay() {
	elements.timerDisplay.textContent = state.timeRemaining + 's';

	// Update timer styling based on remaining time
	elements.timerDisplay.classList.remove('warning', 'danger');
	if (state.timeRemaining <= 10) {
		elements.timerDisplay.classList.add('danger');
	} else if (state.timeRemaining <= 20) {
		elements.timerDisplay.classList.add('warning');
	}
}

function handleTimeUp() {
	clearInterval(state.timerInterval);
	state.hasTimeExpired = true;

	elements.timerDisplay.textContent = '0s';
	elements.timerDisplay.classList.add('danger');

	console.log('=== TIME UP DEBUG ===');
	console.log('Full state.currentQuestion:', state.currentQuestion);
	console.log('Correct answer from state:', state.correctAnswer);
	console.log('Correct answer from currentQuestion:', state.currentQuestion?.correct_answer);

	// Highlight correct answer
	if (state.currentQuestion && state.currentQuestion.correct_answer) {
		state.correctAnswer = state.currentQuestion.correct_answer;
		console.log('Highlighting correct answer:', state.currentQuestion.correct_answer);
		highlightCorrectAnswer(state.currentQuestion.correct_answer);
	} else {
		console.warn('❌ No correct_answer found in question data!');
		console.warn('Available fields:', Object.keys(state.currentQuestion || {}));
	}

	// Show correct answer in timer area
	elements.timerDisplay.innerHTML = '<i class="fas fa-check"></i>';
}

function highlightCorrectAnswer(correctOption) {
	const options = elements.optionsGrid.querySelectorAll('.option-item');
	console.log('Found options:', options.length);

	options.forEach(option => {
		console.log('Option:', option.dataset.option, 'Correct:', correctOption, 'Match:', option.dataset.option === correctOption);
		if (option.dataset.option === correctOption) {
			option.classList.add('correct');
			console.log('Added .correct class to option', correctOption);
		}
	});
}

function clearCorrectAnswerHighlighting() {
	const options = elements.optionsGrid.querySelectorAll('.option-item');
	options.forEach(option => {
		option.classList.remove('correct', 'incorrect');
	});
}

// Update stats display
function updateStats() {
	if (state.currentTest) {
		elements.currentQuestion.textContent = state.currentTest.current_question_number || '-';
	}
}

// Utility: Escape HTML
function escapeHtml(text) {
	const div = document.createElement('div');
	div.textContent = text || '';
	return div.innerHTML;
}
</script>
</body>

</html>