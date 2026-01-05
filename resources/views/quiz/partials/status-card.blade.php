{{-- 
    Status Card Partial
    
    Displays participant information and test status.
    Updated dynamically via Pusher events.
    
    Expected variables:
    - $user: User model instance
    - $currentTest: Test model instance
    - $readyCount: Number of ready participants
    - $isReady: Boolean indicating if user is ready
--}}

<div class="row mt-4">
    {{-- Participant Info Card --}}
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

    {{-- Test Status Card --}}
    <div class="col-md-6">
        <div class="card bg-light">
            <div class="card-body" id="test-status-content">
                <h5><i class="fas fa-info-circle"></i> Test Status</h5>
                
                @if ($currentTest)
                    {{-- Status: Waiting --}}
                    @if ($currentTest->isWaiting())
                    <p class="mb-1"><span class="badge bg-warning">Waiting</span></p>
                    <p class="text-muted mb-0">Test is prepared and waiting to start</p>
                    <p class="mt-2"><i class="fas fa-users"></i> <span id="ready-count-display">{{ $readyCount }}</span> participants ready</p>
                    
                    {{-- Status: Active --}}
                    @elseif($currentTest->isActive())
                    <p class="mb-1"><span class="badge bg-success">Active</span></p>
                    <p class="text-muted mb-0">Test is currently in progress</p>
                    @if ($currentTest->isUserReady($user->id))
                    <p class="text-success mt-2"><i class="fas fa-check-circle"></i> You are participating</p>
                    @else
                    <p class="text-warning mt-2"><i class="fas fa-exclamation-triangle"></i> You missed the start!</p>
                    @endif
                    
                    {{-- Status: Ended --}}
                    @elseif($currentTest->isEnded())
                    <p class="mb-1"><span class="badge bg-secondary">Ended</span></p>
                    <p class="text-muted mb-0">Test has been completed</p>
                    <a href="{{ route('scoreboard') }}" class="btn btn-sm btn-success mt-2">
                        <i class="fas fa-trophy"></i> View Results
                    </a>
                    @endif
                @else
                    {{-- No test scheduled --}}
                    <p class="mb-1"><span class="badge bg-secondary">No Test</span></p>
                    <p class="text-muted mb-0">No test is currently scheduled</p>
                @endif
            </div>
        </div>
    </div>
</div>