@extends('layouts.app')

@section('title', 'Scoreboard - University Competition')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-trophy"></i> Live Scoreboard</h2>
    <div>
        @if($currentTest)
            @if($currentTest->isActive())
                <span class="badge bg-success fs-6">Live Test</span>
            @elseif($currentTest->isEnded())
                <span class="badge bg-info fs-6">Final Results</span>
            @endif
        @else
            <span class="badge bg-secondary fs-6">No Active Test</span>
        @endif
    </div>
</div>

<!-- Winner Announcement -->
@if($currentTest && $currentTest->isEnded() && $scores->count() > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-gradient bg-success text-white">
            <div class="card-body text-center">
                <i class="fas fa-crown fa-3x mb-3"></i>
                <h3>🏆 Competition Winner 🏆</h3>
                <h2 class="mb-0">{{ $scores->first()->user->name }}</h2>
                <p class="mb-0">{{ $scores->first()->user->university }}</p>
                <p class="mb-0">Score: {{ $scores->first()->total_score }} points</p>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Statistics Overview -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <i class="fas fa-users fa-2x mb-2"></i>
                <h4>{{ $scores->count() }}</h4>
                <p class="mb-0">Participants</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <i class="fas fa-check-circle fa-2x mb-2"></i>
                <h4>{{ $scores->sum('correct_answers') }}</h4>
                <p class="mb-0">Correct Answers</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <i class="fas fa-percentage fa-2x mb-2"></i>
                <h4>{{ $scores->count() > 0 ? round(($scores->sum('correct_answers') / $scores->sum('total_questions')) * 100, 1) : 0 }}%</h4>
                <p class="mb-0">Overall Accuracy</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <i class="fas fa-medal fa-2x mb-2"></i>
                <h4>{{ $scores->first()->total_score ?? 0 }}</h4>
                <p class="mb-0">Highest Score</p>
            </div>
        </div>
    </div>
</div>

<!-- Scoreboard Table -->
<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-list-ol"></i> Rankings</h5>
    </div>
    <div class="card-body">
        @if($scores->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover scoreboard-table">
                    <thead class="table-dark">
                        <tr>
                            <th width="10%">Rank</th>
                            <th width="25%">Participant</th>
                            <th width="20%">University</th>
                            <th width="15%">Score</th>
                            <th width="15%">Correct</th>
                            <th width="15%">Accuracy</th>
                        </tr>
                    </thead>
                    <tbody id="scoreboardBody">
                        @foreach($scores as $score)
                        <tr class="{{ $loop->first && $currentTest && $currentTest->isEnded() ? 'table-warning' : '' }}">
                            <td>
                                @if($score->rank == 1)
                                    <span class="badge bg-warning fs-6">🥇 {{ $score->rank }}</span>
                                @elseif($score->rank == 2)
                                    <span class="badge bg-secondary fs-6">🥈 {{ $score->rank }}</span>
                                @elseif($score->rank == 3)
                                    <span class="badge bg-danger fs-6">🥉 {{ $score->rank }}</span>
                                @else
                                    <span class="badge bg-dark fs-6">{{ $score->rank }}</span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $score->user->name }}</strong>
                                @if($score->rank == 1 && $currentTest && $currentTest->isEnded())
                                    <i class="fas fa-crown text-warning ms-1"></i>
                                @endif
                            </td>
                            <td>{{ $score->user->university ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-primary fs-6">{{ $score->total_score }}</span>
                            </td>
                            <td>
                                <span class="badge bg-success fs-6">{{ $score->correct_answers }}</span>
                            </td>
                            <td>
                                @if($score->total_questions > 0)
                                    <span class="badge bg-info fs-6">{{ $score->getAccuracyPercentage() }}%</span>
                                @else
                                    <span class="badge bg-secondary fs-6">0%</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No scores available</h5>
                <p class="text-muted">Scores will appear here once participants start answering questions.</p>
            </div>
        @endif
    </div>
</div>

<!-- Real-time Update Indicator -->
@if($currentTest && $currentTest->isActive())
<div class="mt-3">
    <div class="alert alert-info">
        <i class="fas fa-sync-alt fa-spin"></i> 
        <strong>Live Updates:</strong> This scoreboard updates in real-time as participants answer questions.
    </div>
</div>
@endif

<style>
.scoreboard-table tbody tr:first-child {
    border-left: 4px solid #ffc107;
}

.badge.fs-6 {
    font-size: 0.875rem !important;
}

.table-warning {
    background-color: rgba(255, 193, 7, 0.1) !important;
}
</style>

@section('scripts')
<script>
// Auto-refresh for live scoreboard
@if($currentTest && $currentTest->isActive())
function updateScoreboard() {
    fetch('/scoreboard/live')
        .then(response => response.json())
        .then(data => {
            if (data.scores) {
                updateScoreboardTable(data.scores);
            }
        })
        .catch(error => console.error('Error updating scoreboard:', error));
}

function updateScoreboardTable(scores) {
    const tbody = document.getElementById('scoreboardBody');
    if (!tbody) return;
    
    let html = '';
    scores.forEach((score, index) => {
        const rankBadge = getRankBadge(score.rank);
        const isWinner = score.rank === 1 ? 'table-warning' : '';
        
        html += `
            <tr class="${isWinner}">
                <td>${rankBadge}</td>
                <td><strong>${score.user_name}</strong>${score.rank === 1 ? ' <i class="fas fa-crown text-warning ms-1"></i>' : ''}</td>
                <td>${score.university || 'N/A'}</td>
                <td><span class="badge bg-primary fs-6">${score.total_score}</span></td>
                <td><span class="badge bg-success fs-6">${score.correct_answers}</span></td>
                <td><span class="badge bg-info fs-6">${score.accuracy}%</span></td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

function getRankBadge(rank) {
    if (rank === 1) {
        return '<span class="badge bg-warning fs-6">🥇 ' + rank + '</span>';
    } else if (rank === 2) {
        return '<span class="badge bg-secondary fs-6">🥈 ' + rank + '</span>';
    } else if (rank === 3) {
        return '<span class="badge bg-danger fs-6">🥉 ' + rank + '</span>';
    } else {
        return '<span class="badge bg-dark fs-6">' + rank + '</span>';
    }
}

// Update scoreboard every 5 seconds
setInterval(updateScoreboard, 5000);
@endif
@endsection
@endsection
