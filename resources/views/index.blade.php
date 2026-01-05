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

        html, body {
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
            transition: all 0.3s ease;
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

        .scoreboard-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            max-width: 800px;
            margin: 0 auto;
        }

        .scoreboard-header {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 50%, #d97706 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .scoreboard-header h2 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .scoreboard-header .trophy-icon {
            font-size: 3rem;
            margin-bottom: 0.5rem;
        }

        .scoreboard-body {
            padding: 1.5rem;
        }

        .score-row {
            display: flex;
            align-items: center;
            padding: 1rem 1.25rem;
            border-radius: 12px;
            margin-bottom: 0.5rem;
            transition: transform 0.2s ease;
        }

        .score-row:hover {
            transform: scale(1.02);
        }

        .score-row.top-1 {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 2px solid #fbbf24;
        }

        .score-row.top-2 {
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            border: 2px solid #94a3b8;
        }

        .score-row.top-3 {
            background: linear-gradient(135deg, #fed7aa 0%, #fdba74 100%);
            border: 2px solid #f97316;
        }

        .score-row .rank {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.2rem;
            margin-right: 1rem;
            background: white;
            color: #475569;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .score-row.top-1 .rank {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            color: white;
        }

        .score-row.top-2 .rank {
            background: linear-gradient(135deg, #94a3b8 0%, #64748b 100%);
            color: white;
        }

        .score-row.top-3 .rank {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            color: white;
        }

        .score-row .participant-info {
            flex: 1;
        }

        .score-row .participant-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1e293b;
        }

        .score-row .participant-university {
            font-size: 0.85rem;
            color: #64748b;
        }

        .score-row .score {
            font-size: 1.5rem;
            font-weight: 700;
            color: #10b981;
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
            .competition-header h1 {
                font-size: 1.8rem;
            }

            .stat-card .stat-value {
                font-size: 1.6rem;
            }

            .question-text {
                font-size: 1.2rem;
            }
        }

        @media (max-width: 768px) {
            .guest-container {
                height: auto;
                min-height: 100vh;
            }

            html, body {
                overflow: auto;
            }

            .stats-section {
                padding: 0.75rem 1rem;
            }

            .stat-card {
                margin-bottom: 0.5rem;
            }

            .main-content {
                padding: 0.75rem 1rem;
            }
        }

        /* Animation for new data */
        @keyframes highlight {
            0% { background: #dbeafe; }
            100% { background: transparent; }
        }

        .highlight-row {
            animation: highlight 2s ease-out;
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
            <h1><i class="fas fa-graduation-cap"></i> University Competition 2025</h1>
            <p class="subtitle">Live Competition View - Real-time Updates</p>
        </header>

        <!-- Part 2: Four Stats Cards -->
        <section class="stats-section">
            <div class="row g-3">
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card primary">
                        <div class="icon-wrapper">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-value" id="total-participants">0</div>
                        <div class="stat-label">Total Participants</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card success">
                        <div class="icon-wrapper">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-value" id="ready-count">0</div>
                        <div class="stat-label">Ready</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card warning">
                        <div class="icon-wrapper">
                            <i class="fas fa-question-circle"></i>
                        </div>
                        <div class="stat-value" id="current-question">-</div>
                        <div class="stat-label">Current Question</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card info">
                        <div class="icon-wrapper">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-value" id="time-remaining">--</div>
                        <div class="stat-label">Time Remaining</div>
                    </div>
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
                    <div class="panel-body">
                        <table class="table table-hover participants-table">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-user me-2"></i>Name</th>
                                    <th><i class="fas fa-university me-2"></i>University</th>
                                    <th><i class="fas fa-info-circle me-2"></i>Status</th>
                                    <th><i class="fas fa-check me-2"></i>Answer</th>
                                </tr>
                            </thead>
                            <tbody id="participants-table-body">
                                <!-- Dynamic content will be inserted here -->
                            </tbody>
                        </table>
                    </div>
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
                            <div class="question-text" id="question-text">
                                Loading question...
                            </div>
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

                <!-- Scoreboard (Hidden by default, shown when test ends) -->
                <div class="scoreboard-container" id="scoreboard-container">
                    <div class="scoreboard-card">
                        <div class="scoreboard-header">
                            <div class="trophy-icon">
                                <i class="fas fa-trophy"></i>
                            </div>
                            <h2>Competition Results</h2>
                            <p>Final Standings</p>
                        </div>
                        <div class="scoreboard-body" id="scoreboard-body">
                            <!-- Dynamic scoreboard content -->
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
            participants: [],
            correctAnswer: null,
            hasTimeExpired: false
        };

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
            participantsTableBody: document.getElementById('participants-table-body'),
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

            // Subscribe to Pusher events
            subscribeToChannel();

            // Fetch initial data
            fetchInitialData();

            // Start auto-refresh for data
            setInterval(fetchInitialData, 5000);
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
        }

        // Event handlers
        function handleEvent(eventName, data) {
            // Central event handling
        }

        function handleTestStarted(data) {
            state.currentTest = data.test || data;
            updateStats();
        }

        function handleTestUpdated(data) {
            if (data.stats) {
                elements.totalParticipants.textContent = data.stats.total_users || 0;
                elements.readyCount.textContent = data.stats.ready_participants || 0;
            }

            if (data.participants && Array.isArray(data.participants)) {
                updateParticipantsTable(data.participants);
            }
        }

        function handleParticipantReady(data) {
            if (data.ready_count !== undefined) {
                elements.readyCount.textContent = data.ready_count;
            }

            // Add or update participant in the list
            if (data.user_name) {
                addOrUpdateParticipant({
                    id: data.user_id,
                    name: data.user_name,
                    university: data.university || 'N/A',
                    status: 'ready',
                    has_answered: false,
                    selected_answer: null
                });
            }
        }

        function handleQuestionStarted(data) {
            const question = data.question || data;
            state.currentQuestion = question;
            state.questionStartTime = data.question_start_time || Math.floor(Date.now() / 1000);
            state.timeLimit = data.time_limit || 35;
            state.hasTimeExpired = false;
            state.correctAnswer = null;

            // Clear previous correct answer highlighting
            clearCorrectAnswerHighlighting();

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

            // Start timer
            startTimer();
        }

        function handleAnswerReceived(data) {
            // Update participant's answer status in table
            updateParticipantAnswer(data.user_id, data.selected_answer);
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

            // Hide split panels
            elements.participantsPanel.style.display = 'none';
            elements.questionPanel.style.display = 'none';

            // Show scoreboard
            elements.scoreboardContainer.classList.add('active');
            elements.contentWrapper.classList.add('ended-mode');

            // Generate scoreboard
            if (data.scoreboard || data.scores) {
                generateScoreboard(data.scoreboard || data.scores);
            } else {
                // Fallback: use current participants data
                generateScoreboard(state.participants);
            }
        }

        // Fetch initial data from server
        async function fetchInitialData() {
            try {
                const response = await axios.get('/guest/data');
                const data = response.data;

                // Update stats
                if (data.stats) {
                    elements.totalParticipants.textContent = data.stats.total_users || 0;
                    elements.readyCount.textContent = data.stats.ready_participants || 0;
                }

                // Update participants
                if (data.participants) {
                    state.participants = data.participants;
                    updateParticipantsTable(data.participants);
                }

                // Handle active question
                if (data.currentQuestion && data.currentTest?.is_active) {
                    handleQuestionStarted({
                        ...data.currentQuestion,
                        question_start_time: data.currentTest.question_start_time,
                        time_limit: data.currentTest.time_limit || 35
                    });
                }

                // Handle ended test
                if (data.currentTest?.is_ended) {
                    handleTestEnded({ scoreboard: data.scoreboard });
                }

                console.log('Initial data fetched:', data);
            } catch (error) {
                console.error('Error fetching initial data:', error);
                // Use mock data for demo
                loadMockData();
            }
        }

        // Update participants table
        function updateParticipantsTable(participants) {
            state.participants = participants;
            elements.participantCountBadge.textContent = participants.length;

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

            elements.participantsTableBody.innerHTML = html || '<tr><td colspan="4" class="text-center text-muted py-4">No participants yet</td></tr>';
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

            // Highlight correct answer
            if (state.currentQuestion && state.currentQuestion.correct_answer) {
                state.correctAnswer = state.currentQuestion.correct_answer;
                highlightCorrectAnswer(state.currentQuestion.correct_answer);
            }

            // Show correct answer in timer area
            elements.timerDisplay.innerHTML = '<i class="fas fa-check"></i>';
        }

        function highlightCorrectAnswer(correctOption) {
            const options = elements.optionsGrid.querySelectorAll('.option-item');
            options.forEach(option => {
                if (option.dataset.option === correctOption) {
                    option.classList.add('correct');
                }
            });
        }

        function clearCorrectAnswerHighlighting() {
            const options = elements.optionsGrid.querySelectorAll('.option-item');
            options.forEach(option => {
                option.classList.remove('correct', 'incorrect');
            });
        }

        // Generate scoreboard
        function generateScoreboard(scores) {
            // Sort by score (descending)
            const sorted = [...scores].sort((a, b) => (b.score || 0) - (a.score || 0));

            let html = '';
            sorted.forEach((item, index) => {
                const rank = index + 1;
                let rankClass = '';
                let topClass = '';

                if (rank === 1) {
                    rankClass = '<i class="fas fa-crown"></i>';
                    topClass = 'top-1';
                } else if (rank === 2) {
                    rankClass = '2';
                    topClass = 'top-2';
                } else if (rank === 3) {
                    rankClass = '3';
                    topClass = 'top-3';
                }

                html += `
                    <div class="score-row ${topClass}">
                        <div class="rank">${rankClass}</div>
                        <div class="participant-info">
                            <div class="participant-name">${escapeHtml(item.name)}</div>
                            <div class="participant-university">${escapeHtml(item.university || 'N/A')}</div>
                        </div>
                        <div class="score">${item.score || 0} pts</div>
                    </div>
                `;
            });

            elements.scoreboardBody.innerHTML = html || '<p class="text-center text-muted">No scores available</p>';
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

        // Load mock data for demo purposes
        function loadMockData() {
            console.log('Loading mock data for demo...');

            // Mock participants
            const mockParticipants = [
                { id: 1, name: 'Ahmed Mohamed', university: 'Cairo University', status: 'ready', has_answered: true, selected_answer: 'A' },
                { id: 2, name: 'Fatima Ali', university: 'American University', status: 'ready', has_answered: true, selected_answer: 'B' },
                { id: 3, name: 'Omar Hassan', university: 'Tech University', status: 'waiting', has_answered: false, selected_answer: null },
                { id: 4, name: 'Sara Ahmed', university: 'Engineering College', status: 'ready', has_answered: false, selected_answer: null },
                { id: 5, name: 'Mohamed Ali', university: 'Science University', status: 'ready', has_answered: true, selected_answer: 'C' },
                { id: 6, name: 'Layla Mohamed', university: 'Arts Academy', status: 'ready', has_answered: true, selected_answer: 'A' },
                { id: 7, name: 'Youssef Fatma', university: 'Medical School', status: 'ready', has_answered: false, selected_answer: null },
                { id: 8, name: 'Amira Hassan', university: 'Law School', status: 'waiting', has_answered: false, selected_answer: null }
            ];

            state.participants = mockParticipants;
            updateParticipantsTable(mockParticipants);
            elements.totalParticipants.textContent = '8';
            elements.readyCount.textContent = '6';

            // Simulate a question after a delay
            setTimeout(() => {
                handleQuestionStarted({
                    id: 1,
                    question_number: 1,
                    title: 'What is the capital of France?',
                    option_a: 'London',
                    option_b: 'Paris',
                    option_c: 'Berlin',
                    option_d: 'Madrid',
                    correct_answer: 'B',
                    time_limit: 35
                });
            }, 3000);
        }
    </script>
</body>

</html>
