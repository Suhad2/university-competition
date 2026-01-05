/**
 * Quiz Application JavaScript
 * 
 * This module handles all client-side logic for the quiz interface:
 * - Pusher real-time event handling
 * - Timer management
 * - Answer selection and submission
 * - UI updates
 * 
 * All real-time updates flow through Pusher - no polling is used.
 * 
 * @version 1.0.0
 * @updated 2026-01-04
 */

(function() {
    'use strict';

    /**
     * QuizApp - Main application namespace
     * Organizes all quiz-related functionality into logical modules
     */
    window.QuizApp = {
        // Application state
        state: {
            connected: false,
            selectedAnswer: null,
            hasAnswered: false,
            currentQuestionId: null,
            timeRemaining: 35,
            timerInterval: null
        },

        // Configuration loaded from server
        config: {
            channelName: 'quiz-participants',
            timeLimit: 35
        },

        /**
         * Initialize the quiz application
         */
        init: function() {
            console.log('QuizApp: Initializing...');
            
            // Initialize Pusher connection
            this.pusher.init();
            
            // Set up initial state from DOM
            this.state.hasAnswered = document.getElementById('hasAnswered')?.value === 'true';
            this.state.currentQuestionId = document.getElementById('currentQuestionId')?.value;
            
            // Start timer if question is already active
            if (this.state.currentQuestionId && !this.state.hasAnswered) {
                this.timer.start();
            }
            
            console.log('QuizApp: Initialized successfully');
        },

        /**
         * Pusher event handling module
         */
        pusher: {
            channel: null,

            /**
             * Initialize Pusher connection and subscribe to channel
             */
            init: function() {
                if (!window.Echo) {
                    console.error('QuizApp: Echo/Pusher not available');
                    this.updateConnectionStatus(false);
                    return;
                }

                console.log('QuizApp: Connecting to Pusher...');
                this.updateConnectionStatus(true);

                // Subscribe to the quiz participants channel
                this.channel = Echo.channel(QuizApp.config.channelName);

                // Handle successful subscription
                this.channel.subscribed(function() {
                    console.log('QuizApp: Successfully subscribed to channel');
                    QuizApp.pusher.updateConnectionStatus(true);
                }).error(function(error) {
                    console.error('QuizApp: Subscription error:', error);
                    QuizApp.pusher.updateConnectionStatus(false);
                });

                // Listen for all events (debugging)
                this.channel.listen('*', function(e) {
                    console.log('QuizApp: Event received:', e.event);
                });

                // Set up specific event listeners
                this.bindEvents();
            },

            /**
             * Bind all Pusher event listeners
             */
            bindEvents: function() {
                // Test started event
                this.channel.listen('.test.started', function(e) {
                    console.log('QuizApp: Test started event received');
                    QuizApp.events.handleTestStarted(e);
                });

                // Question started event
                this.channel.listen('.question.started', function(e) {
                    console.log('QuizApp: Question started event received');
                    QuizApp.events.handleQuestionStarted(e);
                });

                // Test ended event
                this.channel.listen('.test.ended', function(e) {
                    console.log('QuizApp: Test ended event received');
                    QuizApp.events.handleTestEnded(e);
                });

                // Participant ready event
                this.channel.listen('.participant.ready', function(e) {
                    console.log('QuizApp: Participant ready event received');
                    QuizApp.events.handleParticipantReady(e);
                });

                // Test updated event
                this.channel.listen('.test.updated', function(e) {
                    console.log('QuizApp: Test updated event received');
                    QuizApp.events.handleTestUpdated(e);
                });
            },

            /**
             * Update connection status indicator
             */
            updateConnectionStatus: function(connected) {
                QuizApp.state.connected = connected;
                const statusEl = document.getElementById('connection-status');
                if (statusEl) {
                    if (connected) {
                        statusEl.innerHTML = '<i class="fas fa-wifi text-success"></i> <span class="text-success">Connected</span>';
                    } else {
                        statusEl.innerHTML = '<i class="fas fa-wifi text-danger"></i> <span class="text-danger">Disconnected</span>';
                    }
                    statusEl.classList.remove('d-none');
                }
            }
        },

        /**
         * Event handlers for Pusher events
         */
        events: {
            /**
             * Handle test started event
             */
            handleTestStarted: function(event) {
                QuizApp.ui.showNotification('Test is Ready! Waiting for participants...');
                
                // Show waiting container
                QuizApp.ui.showWaitingContainer();
                
                // Update test status display
                QuizApp.ui.updateTestStatus('waiting', 0);
                
                // Hide after notification
                setTimeout(function() {
                    QuizApp.ui.hideNotification();
                }, 3000);
            },

            /**
             * Handle new question event
             */
            handleQuestionStarted: function(event) {
                const question = event.question || event.data?.question;
                const questionStartTime = event.question_start_time || event.data?.question_start_time || Math.floor(Date.now() / 1000);
                const timeLimit = event.time_limit || event.data?.time_limit || 35;

                if (!question) {
                    console.error('QuizApp: No question data in event');
                    return;
                }

                QuizApp.ui.showNotification('New question received!');

                // Hide waiting containers
                QuizApp.ui.hideWaitingContainer();
                QuizApp.ui.hideWaitingForNextContainer();

                // Update and show question container
                QuizApp.ui.updateQuestion(question, questionStartTime, timeLimit);

                // Update test status
                QuizApp.ui.updateTestStatus('active', null);

                // Hide notification after delay
                setTimeout(function() {
                    QuizApp.ui.hideNotification();
                }, 3000);
            },

            /**
             * Handle test ended event
             */
            handleTestEnded: function(event) {
                QuizApp.ui.showNotification('Test has ended!');
                
                // Stop timer
                QuizApp.timer.stop();
                
                // Hide question container
                QuizApp.ui.hideQuestionContainer();
                
                // Show ended status
                QuizApp.ui.showTestEnded(event.redirect_url || '/scoreboard');
                
                // Hide notification after delay
                setTimeout(function() {
                    QuizApp.ui.hideNotification();
                }, 3000);
            },

            /**
             * Handle participant ready event (update ready count)
             */
            handleParticipantReady: function(event) {
                if (event.ready_count !== undefined) {
                    QuizApp.ui.updateReadyCount(event.ready_count);
                }
                
                if (event.user_name) {
                    QuizApp.ui.showNotification(event.user_name + ' is ready to participate!');
                    setTimeout(function() {
                        QuizApp.ui.hideNotification();
                    }, 3000);
                }
            },

            /**
             * Handle test updated event (general updates)
             */
            handleTestUpdated: function(event) {
                console.log('QuizApp: Processing test update:', event);
                // Handle any general test updates here
                if (event.stats) {
                    QuizApp.ui.updateStats(event.stats);
                }
            }
        },

        /**
         * Timer management module
         */
        timer: {
            /**
             * Start or restart the countdown timer
             */
            start: function(timeLimit, startTime) {
                this.stop(); // Clear any existing timer
                
                const limit = timeLimit || QuizApp.config.timeLimit;
                const start = startTime || parseInt(document.getElementById('startTime')?.value);
                
                // Calculate remaining time
                if (start) {
                    const elapsed = Math.floor((Date.now() / 1000) - start);
                    QuizApp.state.timeRemaining = Math.max(0, limit - elapsed);
                } else {
                    QuizApp.state.timeRemaining = limit;
                }

                // Update display immediately
                this.updateDisplay();

                // Start countdown
                QuizApp.state.timerInterval = setInterval(function() {
                    QuizApp.state.timeRemaining--;
                    QuizApp.timer.updateDisplay();

                    if (QuizApp.state.timeRemaining <= 0) {
                        QuizApp.timer.stop();
                        QuizApp.timer.handleTimeUp();
                    }
                }, 1000);
            },

            /**
             * Stop the timer
             */
            stop: function() {
                if (QuizApp.state.timerInterval) {
                    clearInterval(QuizApp.state.timerInterval);
                    QuizApp.state.timerInterval = null;
                }
            },

            /**
             * Update timer display
             */
            updateDisplay: function() {
                const timerEl = document.getElementById('timer');
                if (timerEl) {
                    timerEl.textContent = QuizApp.state.timeRemaining + 's';
                    
                    // Update color based on remaining time
                    if (QuizApp.state.timeRemaining <= 10) {
                        timerEl.style.color = '#dc3545'; // Red
                    } else if (QuizApp.state.timeRemaining <= 20) {
                        timerEl.style.color = '#ffc107'; // Yellow
                    } else {
                        timerEl.style.color = '';
                    }
                }
            },

            /**
             * Handle timer expiration
             */
            handleTimeUp: function() {
                // Show time up message
                QuizApp.ui.showStatusMessage('Time\'s up! You can\'t answer anymore', 'danger');
                
                // Disable answer options
                QuizApp.ui.disableAnswerOptions();
                
                // Disable submit button
                QuizApp.ui.disableSubmitButton('Time\'s Up');
            }
        },

        /**
         * User actions module
         */
        actions: {
            /**
             * Select an answer option
             */
            selectAnswer: function(answer) {
                if (QuizApp.state.hasAnswered) {
                    console.log('QuizApp: Already answered, ignoring selection');
                    return;
                }

                // Update state
                QuizApp.state.selectedAnswer = answer;
                
                // Update UI - remove selected from all, add to current
                document.querySelectorAll('.answer-option').forEach(function(opt) {
                    opt.classList.remove('selected');
                    if (opt.dataset.answer === answer) {
                        opt.classList.add('selected');
                    }
                });

                // Enable submit button
                QuizApp.ui.enableSubmitButton();
            },

            /**
             * Mark user as ready to participate
             */
            markAsReady: function() {
                const btn = event.target;
                if (btn.disabled) return;
                
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Processing...';

                fetch('/quiz/mark-ready', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                })
                .then(function(response) { return response.json(); })
                .then(function(data) {
                    if (data.success) {
                        QuizApp.ui.showNotification('You are ready to participate!');
                        
                        // Update button state
                        btn.innerHTML = '<i class="fas fa-check"></i> You\'re Ready!';
                        btn.classList.remove('btn-warning');
                        btn.classList.add('btn-success');
                        
                        // Update ready count if available
                        if (data.readyCount !== undefined) {
                            QuizApp.ui.updateReadyCount(data.readyCount);
                        }
                    } else {
                        QuizApp.ui.showNotification(data.error || 'Error marking as ready');
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-hand-paper"></i> I\'m Ready to Participate';
                    }
                })
                .catch(function(error) {
                    console.error('QuizApp: Error marking as ready:', error);
                    QuizApp.ui.showNotification('An error occurred. Please try again.');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-hand-paper"></i> I\'m Ready to Participate';
                });
            },

            /**
             * Submit the selected answer
             */
            submitAnswer: function() {
                if (!QuizApp.state.selectedAnswer) {
                    QuizApp.ui.showNotification('Please select an answer first!');
                    return;
                }
                
                const questionId = document.getElementById('questionId')?.value;
                const submitBtn = document.getElementById('submitAnswerBtn');
                
                if (!questionId) {
                    QuizApp.ui.showNotification('No question found!');
                    return;
                }
                
                // Disable button to prevent double submission
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Submitting...';

                fetch('/quiz/answer', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    },
                    body: JSON.stringify({
                        selected_answer: QuizApp.state.selectedAnswer,
                        question_id: questionId
                    })
                })
                .then(function(response) { return response.json(); })
                .then(function(data) {
                    if (data.success) {
                        // Mark as answered
                        QuizApp.state.hasAnswered = true;
                        
                        // Show success message
                        QuizApp.ui.showStatusMessage('Answer submitted successfully!', 'success');
                        
                        // Stop timer
                        QuizApp.timer.stop();
                        
                        // Hide question, show waiting for next
                        QuizApp.ui.hideQuestionContainer();
                        QuizApp.ui.showWaitingForNextContainer();
                    } else {
                        QuizApp.ui.showNotification(data.error || 'Error submitting answer');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-check"></i> Submit Answer';
                    }
                })
                .catch(function(error) {
                    console.error('QuizApp: Error submitting answer:', error);
                    QuizApp.ui.showNotification('An error occurred. Please try again.');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-check"></i> Submit Answer';
                });
            }
        },

        /**
         * UI manipulation module
         */
        ui: {
            /**
             * Show notification message
             */
            showNotification: function(message) {
                const notification = document.getElementById('update-notification');
                const notificationMessage = document.getElementById('notification-message');
                
                if (notification && notificationMessage) {
                    notificationMessage.textContent = message;
                    notification.classList.remove('d-none');
                }
            },

            /**
             * Hide notification
             */
            hideNotification: function() {
                const notification = document.getElementById('update-notification');
                if (notification) {
                    notification.classList.add('d-none');
                }
            },

            /**
             * Show waiting container
             */
            showWaitingContainer: function() {
                const container = document.getElementById('waiting-container');
                if (container) {
                    container.classList.remove('d-none');
                }
            },

            /**
             * Hide waiting container
             */
            hideWaitingContainer: function() {
                const container = document.getElementById('waiting-container');
                if (container) {
                    container.classList.add('d-none');
                }
            },

            /**
             * Show waiting for next container
             */
            showWaitingForNextContainer: function() {
                const container = document.getElementById('waiting-for-next-container');
                if (container) {
                    container.classList.remove('d-none');
                }
            },

            /**
             * Hide waiting for next container
             */
            hideWaitingForNextContainer: function() {
                const container = document.getElementById('waiting-for-next-container');
                if (container) {
                    container.classList.add('d-none');
                }
            },

            /**
             * Show question container and update content
             */
            updateQuestion: function(question, startTime, timeLimit) {
                const container = document.getElementById('question-container');
                if (!container) return;

                // Show container
                container.classList.remove('d-none');

                // Clear status message
                this.clearStatusMessage();

                // Update question text
                const questionTitle = container.querySelector('.question-content h4');
                if (questionTitle) {
                    questionTitle.textContent = question.title;
                }

                // Update options
                this.updateOption('option-a', question.option_a);
                this.updateOption('option-b', question.option_b);
                this.updateOption('option-c', question.option_c);
                this.updateOption('option-d', question.option_d);

                // Update hidden inputs
                this.updateHiddenInput('questionId', question.id);
                this.updateHiddenInput('currentQuestionId', question.id);
                this.updateHiddenInput('startTime', startTime);
                this.updateHiddenInput('timeLimit', timeLimit);
                this.updateHiddenInput('hasAnswered', 'false');

                // Reset state
                QuizApp.state.hasAnswered = false;
                QuizApp.state.selectedAnswer = null;
                QuizApp.state.currentQuestionId = question.id;

                // Reset option styles
                this.resetOptionStyles();

                // Reset submit button
                this.disableSubmitButton('Submit Answer');

                // Start timer
                QuizApp.timer.start(timeLimit, startTime);
            },

            /**
             * Hide question container
             */
            hideQuestionContainer: function() {
                const container = document.getElementById('question-container');
                if (container) {
                    container.classList.add('d-none');
                }
            },

            /**
             * Update a single option text
             */
            updateOption: function(elementId, text) {
                const el = document.getElementById(elementId);
                if (el) {
                    el.textContent = text;
                }
            },

            /**
             * Update hidden input value
             */
            updateHiddenInput: function(id, value) {
                const el = document.getElementById(id);
                if (el) {
                    el.value = value;
                }
            },

            /**
             * Reset all option styles
             */
            resetOptionStyles: function() {
                document.querySelectorAll('.answer-option').forEach(function(opt) {
                    opt.classList.remove('selected');
                    opt.style.pointerEvents = 'auto';
                    opt.style.opacity = '1';
                });
            },

            /**
             * Disable all answer options
             */
            disableAnswerOptions: function() {
                document.querySelectorAll('.answer-option').forEach(function(opt) {
                    opt.style.pointerEvents = 'none';
                    opt.style.opacity = '0.6';
                });
            },

            /**
             * Enable submit button
             */
            enableSubmitButton: function() {
                const btn = document.getElementById('submitAnswerBtn');
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-check"></i> Submit Answer';
                }
            },

            /**
             * Disable submit button
             */
            disableSubmitButton: function(text) {
                const btn = document.getElementById('submitAnswerBtn');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-clock"></i> ' + (text || 'Submit Answer');
                }
            },

            /**
             * Show status message
             */
            showStatusMessage: function(message, type) {
                const statusMessage = document.getElementById('statusMessage');
                if (statusMessage) {
                    statusMessage.innerHTML = '<div class="alert alert-' + type + '"><i class="fas fa-' + 
                        (type === 'success' ? 'check-circle' : 'times-circle') + '"></i> ' + message + '</div>';
                }
            },

            /**
             * Clear status message
             */
            clearStatusMessage: function() {
                const statusMessage = document.getElementById('statusMessage');
                if (statusMessage) {
                    statusMessage.innerHTML = '';
                }
            },

            /**
             * Update ready count display
             */
            updateReadyCount: function(count) {
                const countEl = document.getElementById('ready-count-display');
                if (countEl) {
                    countEl.textContent = count;
                }
                
                // Also update in waiting message if exists
                const leadEl = document.querySelector('.lead');
                if (leadEl) {
                    leadEl.innerHTML = leadEl.innerHTML.replace(/\d+\s+participants/, count + ' participants');
                }
            },

            /**
             * Update test status display
             */
            updateTestStatus: function(status, readyCount) {
                const statusContent = document.getElementById('test-status-content');
                if (!statusContent) return;

                let html = '';

                if (status === 'waiting') {
                    html = '<p class="mb-1"><span class="badge bg-warning">Waiting</span></p>' +
                           '<p class="text-muted mb-0">Test is prepared and waiting to start</p>';
                    if (readyCount !== null && readyCount !== undefined) {
                        html += '<p class="mt-2"><i class="fas fa-users"></i> ' + readyCount + ' participants ready</p>';
                    }
                } else if (status === 'active') {
                    html = '<p class="mb-1"><span class="badge bg-success">Active</span></p>' +
                           '<p class="text-muted mb-0">Test is currently in progress</p>' +
                           '<p class="text-success mt-2"><i class="fas fa-check-circle"></i> You are participating</p>';
                }

                statusContent.innerHTML = html;
            },

            /**
             * Show test ended status
             */
            showTestEnded: function(redirectUrl) {
                const waitingContainer = document.getElementById('waiting-container');
                if (waitingContainer) {
                    waitingContainer.classList.remove('d-none');
                    const cardBody = waitingContainer.querySelector('.card-body');
                    if (cardBody) {
                        cardBody.innerHTML = '<div class="mb-4">' +
                            '<i class="fas fa-trophy fa-3x text-success mb-3"></i>' +
                            '<h3>Test Completed!</h3>' +
                            '<p class="lead">Thank you for participating.</p>' +
                            '<a href="' + (redirectUrl || '/scoreboard') + '" class="btn btn-success btn-lg">' +
                            '<i class="fas fa-trophy"></i> View Results</a></div>';
                    }
                }
            },

            /**
             * Update stats display
             */
            updateStats: function(stats) {
                if (stats.ready_participants !== undefined) {
                    this.updateReadyCount(stats.ready_participants);
                }
            },

            /**
             * Scroll to question section
             */
            scrollToQuestion: function() {
                const questionContainer = document.getElementById('question-container');
                if (questionContainer) {
                    questionContainer.scrollIntoView({ behavior: 'smooth' });
                }
            }
        }
    };

    /**
     * Initialize QuizApp when DOM is ready
     */
    document.addEventListener('DOMContentLoaded', function() {
        window.QuizApp.init();
    });

})();