@extends('layouts.app')

@section('title', 'Scoreboard - University Competition')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow">
            <div class="card-header bg-secondary text-white text-center">
                <h4><i class="fas fa-trophy"></i> Scoreboard</h4>
            </div>
            <div class="card-body text-center">
                <div class="py-5">
                    <i class="fas fa-chart-bar fa-4x text-muted mb-4"></i>
                    <h4 class="text-muted">No Test Available</h4>
                    <p class="text-muted">There is no active or completed test to display scores for.</p>
                    <div class="mt-4">
                        <a href="{{ route('dashboard') }}" class="btn btn-primary">
                            <i class="fas fa-home"></i> Return to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
