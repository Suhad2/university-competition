{{-- 
    Waiting Card Partial
    
    Displays waiting status for participants:
    - Shows participation button when test is in waiting status
    - Shows missed start message when test is active but user is not ready
    - Shows waiting message when no test is available
--}}

{{-- Main waiting container - always visible --}}
<div id="waiting-container" class="card shadow">

    {{-- Card Header --}}
    <div class="card-header bg-info text-white text-center">
        <h4><i class="fas fa-graduation-cap"></i> University Competition</h4>
    </div>

    {{-- Card Body --}}
    <div class="card-body text-center">
        
        {{-- Case 1: Test is in waiting status - show participation button --}}
        @if ($currentTest && $currentTest->status === 'waiting')
        <div id="waiting-status">
            <i class="fas fa-hourglass-half fa-3x text-warning mb-3"></i>
            <h3>Test is Ready!</h3>
            <p class="lead">The exam manager has prepared the test.</p>
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong>What to do:</strong>
                <ul class="text-start mb-0 mt-2">
                    <li>Click "Participate" button below to join</li>
                    <li>Stay on this page - questions will appear automatically</li>
                    <li>Keep your browser open</li>
                    <li>Be prepared to answer quickly</li>
                </ul>
            </div>

            {{-- Participation button --}}
            <div id="participation-button-container" class="d-grid mt-4">
                <button class="btn btn-warning btn-lg" onclick="QuizApp.actions.markAsReady()">
                    <i class="fas fa-hand-paper"></i> Participate Now
                </button>
            </div>
        </div>

        {{-- Case 2: Test is active but user missed the start --}}
        @elseif($currentTest && $currentTest->status === 'active' && !$isReady)
        <div id="missed-start">
            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
            <h3>You Missed the Start!</h3>
            <p class="text-muted">The test has already started. Please contact your exam manager.</p>
        </div>

        {{-- Case 3: Test is active and user is ready --}}
        @elseif($currentTest && $currentTest->status === 'active' && $isReady)
        <div id="ready-waiting">
            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
            <h3>You're Ready!</h3>
            <p class="lead">Waiting for the first question...</p>
            <div class="spinner-border text-primary mt-3" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        {{-- Case 4: No test available --}}
        @else
        <div id="no-test">
            <i class="fas fa-clock fa-3x text-muted mb-3"></i>
            <h3>Please wait for the test to start</h3>
            <p class="text-muted">No test is currently scheduled.</p>
        </div>
        @endif

    </div>
</div>