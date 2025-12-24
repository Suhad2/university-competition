@extends('layouts.app')

@section('title', 'Quiz Dashboard - University Competition')

@push('scripts')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
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
                                <div class="card-body">
                                    <h5><i class="fas fa-info-circle"></i> Test Status</h5>
                                    @if ($currentTest)
                                        {{-- حالة الانتظار --}}
                                        @if ($currentTest->isWaiting())
                                            <p class="mb-1"><span class="badge bg-warning">Waiting</span></p>
                                            <p class="text-muted mb-0">Test is prepared and waiting to start</p>
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
                                                <p class="text-success mt-2"><i class="fas fa-check-circle"></i> You are
                                                    participating</p>
                                            @else
                                                <p class="text-warning mt-2"><i class="fas fa-exclamation-triangle"></i> You
                                                    missed the start!</p>
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
                    @if ($currentTest && $currentTest->isWaiting())
                        <div class="d-grid mt-4">
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
                    @endif
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
            <div id="question-container" class="d-none">
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
                                                <span>{{ $question ? $question->option_a : '...' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- الخيار ب --}}
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
                                    {{-- الخيار ج --}}
                                    <div class="col-md-6">
                                        <div class="answer-option" data-answer="C">
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-primary me-3" style="font-size: 1.2rem;">C</span>
                                                <span>{{ $question ? $question->option_c : '...' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- الخيار د --}}
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
        let currentPollingInterval = null;
        let currentQuestionId = {{ $question ? $question->id : 'null' }};
        let partialUpdateEnabled = true; // Enable partial page updates by default

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Initializing quiz dashboard with partial updates enabled...');
            
            @if ($currentTest && $currentTest->isWaiting() && $isReady)
                // Test is waiting and user is ready - start polling for first question
                startPollingForFirstQuestion();
            @elseif ($currentTest && $currentTest->isActive() && $currentTest->isUserReady($user->id))
                @if ($existingAnswer)
                    // User has already answered - show waiting screen
                    showWaitingForNext();
                    // Start polling for next question
                    startPollingForNextQuestion();
                @else
                    // Question is active - initialize timer
                    initializeTimer();
                @endif
            @else
                // No active test or user not ready - start polling for test availability
                startPollingForTestAvailability();
            @endif

            // Attach answer option click handlers
            attachAnswerOptionListeners();
        });

        /**
         * Poll for first question when test is in waiting status
         * Uses partial page update instead of full reload
         */
        function startPollingForFirstQuestion() {
            console.log('Starting poll for first question with partial updates...');
            
            currentPollingInterval = setInterval(async function() {
                try {
                    const response = await fetch('/quiz/realtime-status');
                    const data = await response.json();

                    console.log('Checking for first question:', data);

                    // Check for test ended
                    if (data.exam_ended) {
                        clearInterval(currentPollingInterval);
                        if (data.redirect_url) {
                            window.location.href = data.redirect_url;
                        } else {
                            location.reload();
                        }
                        return;
                    }

                    // Check for active question with HTML (user hasn't answered yet)
                    if (data.test_active && data.has_question && data.html) {
                        console.log('First question detected! Updating page partially...');
                        clearInterval(currentPollingInterval);
                        updateQuestionContainerPartial(data);
                    }

                    // Also check ready count update
                    if (data.test_waiting && data.ready_count !== undefined) {
                        updateReadyCountDisplay(data.ready_count);
                    }

                } catch (error) {
                    console.error('Error polling for first question:', error);
                }
            }, 1000);
        }

        /**
         * Poll for next question after answering current question
         * Uses partial page update instead of full reload
         */
        function startPollingForNextQuestion() {
            console.log('Starting poll for next question with partial updates...');
            
            currentPollingInterval = setInterval(async function() {
                try {
                    const response = await fetch('/quiz/realtime-status');
                    const data = await response.json();

                    console.log('Checking for next question:', data);

                    // Check for test ended
                    if (data.exam_ended) {
                        clearInterval(currentPollingInterval);
                        if (data.redirect_url) {
                            window.location.href = data.redirect_url;
                        } else {
                            location.reload();
                        }
                        return;
                    }

                    // Check for new question
                    if (data.has_question && data.html && data.question_data) {
                        const newQuestionId = data.question_data.id;
                        
                        if (newQuestionId !== currentQuestionId) {
                            console.log('New question detected! Updating page partially...');
                            updateQuestionContainerPartial(data);
                        }
                    }

                } catch (error) {
                    console.error('Error polling for next question:', error);
                }
            }, 1000);
        }

        /**
         * Poll for test availability when no test is active
         * Uses partial page update for status changes
         */
        function startPollingForTestAvailability() {
            console.log('Starting poll for test availability with partial updates...');
            
            currentPollingInterval = setInterval(async function() {
                try {
                    const response = await fetch('/quiz/realtime-status');
                    const data = await response.json();

                    console.log('Checking test availability:', data);

                    // Test became available with waiting status
                    if (data.test_waiting && data.user_is_ready) {
                        console.log('Test available and user ready! Updating partial...');
                        updateWaitingStatusPartial(data);
                        clearInterval(currentPollingInterval);
                        startPollingForFirstQuestion();
                    }
                    // Test became active with question
                    else if (data.test_active && data.has_question && data.html) {
                        console.log('Test active with question! Updating partial...');
                        updateQuestionContainerPartial(data);
                        clearInterval(currentPollingInterval);
                    }
                    // Test ended
                    else if (data.exam_ended) {
                        console.log('Test ended!');
                        clearInterval(currentPollingInterval);
                        if (data.redirect_url) {
                            window.location.href = data.redirect_url;
                        } else {
                            location.reload();
                        }
                    }
                    // Update ready count if visible
                    if (data.test_waiting && data.ready_count !== undefined) {
                        updateReadyCountDisplay(data.ready_count);
                    }

                } catch (error) {
                    console.error('Error polling for test availability:', error);
                }
            }, 1500);
        }

        /**
         * Update question container using partial page update
         * Replaces question container HTML without full page reload
         */
        function updateQuestionContainerPartial(data) {
            console.log('Updating question container partially...');
            
            // Hide other containers
            document.getElementById('waiting-container')?.classList.add('d-none');
            document.getElementById('waiting-for-next-container')?.classList.add('d-none');

            // Get the main container
            const mainContainer = document.querySelector('.col-md-8');
            
            if (mainContainer && data.html) {
                // Replace the question container content
                const questionContainer = document.getElementById('question-container');
                 if (questionContainer) {
                    questionContainer.innerHTML = data.html;
                    // CRITICAL: Remove d-none class to make question visible
                    questionContainer.classList.remove('d-none');
                } else {
                    // If question container doesn't exist, add it
                    mainContainer.innerHTML = data.html;
                    // Show the newly added question container
                    const newQuestionContainer = document.getElementById('question-container');
                    if (newQuestionContainer) {
                        newQuestionContainer.classList.remove('d-none');
                    }
                }

                // Update current question ID
                if (data.question_data) {
                    currentQuestionId = data.question_data.id;
                }

                // Reset state
                hasAnswered = false;
                selectedAnswer = null;
                timeRemaining = 35;

                // Initialize timer for new question
                initializeTimer();

                // Re-attach answer option click handlers
                attachAnswerOptionListeners();

                console.log('Question container updated successfully');
            } else {
                // Fallback to full reload if partial update fails
                console.log('Partial update failed, falling back to full reload...');
                location.reload();
            }
        }

        /**
         * Update waiting status using partial page update
         */
        function updateWaitingStatusPartial(data) {
            console.log('Updating waiting status partially...');
            
            const waitingContainer = document.getElementById('waiting-container');
            if (waitingContainer) {
                waitingContainer.classList.remove('d-none');
            }

            // Update ready count if visible
            if (data.ready_count !== undefined) {
                updateReadyCountDisplay(data.ready_count);
            }
        }

        /**
         * Update ready count display
         */
        function updateReadyCountDisplay(count) {
            const readyCountElement = document.querySelector('#ready-count');
            if (readyCountElement) {
                readyCountElement.textContent = count;
            }
        }

        /**
         * Attach click handlers to answer options
         */
        function attachAnswerOptionListeners() {
            document.querySelectorAll('.answer-option').forEach(option => {
                option.addEventListener('click', function() {
                    // Prevent selection if user has answered or time is up
                    if (hasAnswered || timeRemaining <= 0) return;

                    // Remove previous selections
                    document.querySelectorAll('.answer-option').forEach(opt => {
                        opt.classList.remove('selected');
                    });

                    // Add selection to clicked option
                    this.classList.add('selected');

                    // Store selected answer
                    selectedAnswer = this.dataset.answer;
                });
            });
        }

        /**
         * Show waiting for next question screen
         */
        function showWaitingForNext() {
            document.getElementById('waiting-container')?.classList.add('d-none');
            document.getElementById('question-container')?.classList.add('d-none');
            document.getElementById('waiting-for-next-container')?.classList.remove('d-none');
        }

        /**
         * Initialize timer based on server time
         */
        function initializeTimer() {
            const startTimeEl = document.getElementById('startTime');
            const timeLimitEl = document.getElementById('timeLimit');
            
            if (!startTimeEl || !timeLimitEl) {
                console.error('Timer elements not found');
                return;
            }

            const startTime = parseInt(startTimeEl.value);
            const timeLimit = parseInt(timeLimitEl.value);
            const currentTime = Math.floor(Date.now() / 1000);

            // Calculate remaining time from server timestamp
            const elapsed = currentTime - startTime;
            const calculatedRemaining = timeLimit - elapsed;

            // Use calculated remaining time with validation
            if (calculatedRemaining > 0 && calculatedRemaining <= timeLimit) {
                timeRemaining = calculatedRemaining;
            } else if (calculatedRemaining <= 0) {
                timeRemaining = 0;
            } else {
                timeRemaining = parseInt(timeLimitEl.value);
            }

            // Ensure remaining time doesn't exceed limit
            if (timeRemaining > timeLimit) {
                timeRemaining = timeLimit;
            }

            // Update display immediately
            const timerElement = document.getElementById('timer');
            if (timerElement) {
                timerElement.textContent = timeRemaining + 's';
                timerElement.className = 'timer-display bg-warning';
            }

            // Start countdown
            if (timerInterval) {
                clearInterval(timerInterval);
            }
            
            timerInterval = setInterval(function() {
                timeRemaining--;

                // Time's up
                if (timeRemaining <= 0) {
                    if (timerElement) {
                        timerElement.textContent = '0s';
                        timerElement.className = 'timer-display bg-danger';
                    }
                    clearInterval(timerInterval);
                    disableAnswers();
                    return;
                }

                if (timerElement) {
                    timerElement.textContent = timeRemaining + 's';

                    // Change color based on remaining time
                    if (timeRemaining <= 5) {
                        timerElement.className = 'timer-display bg-danger';
                    } else if (timeRemaining <= 10) {
                        timerElement.className = 'timer-display bg-warning';
                    }
                }
            }, 1000);
        }

        /**
         * Disable answer selection
         */
        function disableAnswers() {
            document.querySelectorAll('.answer-option').forEach(option => {
                option.style.pointerEvents = 'none';
                option.classList.add('disabled');
            });
            
            const submitBtn = document.getElementById('submitAnswerBtn');
            if (submitBtn) {
                submitBtn.style.display = 'none';
            }
            
            hasAnswered = true;
            
            const hasAnsweredEl = document.getElementById('hasAnswered');
            if (hasAnsweredEl) {
                hasAnsweredEl.value = 'true';
            }
        }

        /**
         * Mark user as ready to participate
         */
        function markAsReady() {
            if (!confirm('Are you ready to participate in this test? Make sure to stay on this page until the test ends.')) {
                return;
            }

            console.log('Marking user as ready...');

            fetch('{{ route('quiz.mark-ready') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    // Stop current polling
                    if (currentPollingInterval) {
                        clearInterval(currentPollingInterval);
                    }
                    
                    // Update UI to show ready status
                    updateReadyButtonPartial(data);
                    
                    // Start polling for first question
                    startPollingForFirstQuestion();
                } else {
                    alert(data.error || 'An error occurred. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        }

        /**
         * Update ready button using partial update
         */
        function updateReadyButtonPartial(data) {
            // Update ready count if visible
            updateReadyCountDisplay(data.ready_count);

            // Update ready button state
            const readyBtn = document.querySelector('.btn-warning');
            if (readyBtn) {
                readyBtn.classList.remove('btn-warning');
                readyBtn.classList.add('btn-success');
                readyBtn.disabled = true;
                readyBtn.innerHTML = '<i class="fas fa-check"></i> You\'re Ready!';
                
                // Also update large button if exists
                const largeReadyBtn = document.querySelector('.d-grid .btn-warning');
                if (largeReadyBtn) {
                    largeReadyBtn.classList.remove('btn-warning');
                    largeReadyBtn.classList.add('btn-success');
                    largeReadyBtn.disabled = true;
                    largeReadyBtn.innerHTML = '<i class="fas fa-check"></i> You\'re Ready! Waiting for First Question...';
                }
            }
        }

        /**
         * Submit answer to server
         */
        function submitAnswer() {
            if (!selectedAnswer) {
                showStatus('Please select an answer first!', 'warning');
                return;
            }

            const formData = new FormData();
            formData.append('selected_answer', selectedAnswer);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

            fetch('/quiz/answer', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Disable answers and hide submit button
                    disableAnswers();

                    // Hide question container and show waiting for next
                    document.getElementById('question-container')?.classList.add('d-none');
                    document.getElementById('waiting-for-next-container')?.classList.remove('d-none');

                    // Start polling for next question
                    startPollingForNextQuestion();
                } else {
                    showStatus(data.error || 'Error submitting answer', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showStatus('Error submitting answer', 'error');
            });
        }

        /**
         * Show status message to user
         */
        function showStatus(message, type) {
            const statusElement = document.getElementById('statusMessage');
            if (statusElement) {
                const alertClass = type === 'warning' ? 'warning' : (type === 'success' ? 'success' : 'danger');
                statusElement.innerHTML = `<div class="alert alert-${alertClass}">${message}</div>`;
            }
        }
    </script>
@endsection
