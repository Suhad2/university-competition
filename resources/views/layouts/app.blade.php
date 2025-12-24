<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'University Competition')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .timer-display {
            font-size: 2rem;
            font-weight: bold;
            color: #dc3545;
            text-align: center;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
            margin: 20px 0;
        }
        .question-card {
            max-width: 800px;
            margin: 0 auto;
        }
        .answer-option {
            margin: 10px 0;
            padding: 15px;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .answer-option:hover {
            border-color: #007bff;
            background-color: #f8f9fa;
        }
        .answer-option.selected {
            border-color: #007bff;
            background-color: #e7f1ff;
        }
        .correct-answer {
            border-color: #28a745 !important;
            background-color: #d4edda !important;
        }
        .incorrect-answer {
            border-color: #dc3545 !important;
            background-color: #f8d7da !important;
        }
        .scoreboard-table {
            font-size: 1.1rem;
        }
        .navbar-brand {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="fas fa-graduation-cap"></i> University Competition
            </a>
            
            @auth
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user"></i> {{ Auth::user()->name }}
                    </a>
                    <ul class="dropdown-menu">
                        <li><span class="dropdown-item-text"><strong>Role:</strong> {{ ucfirst(str_replace('_', ' ', Auth::user()->role)) }}</span></li>
                        @if(Auth::user()->university)
                        <li><span class="dropdown-item-text"><strong>University:</strong> {{ Auth::user()->university }}</span></li>
                        @endif
                       <li><hr class="dropdown-divider"></li>
@if(Auth::user() && !(isset($currentTest) && $currentTest && $currentTest->isActive() && $currentTest->isUserReady(Auth::id())))
<li><a class="dropdown-item dashboard-link" href="{{ route('dashboard') }}"><i class="fas fa-play-circle"></i> Dashboard</a></li>
@endif
<li><a class="dropdown-item" href="{{ route('scoreboard') }}"><i class="fas fa-trophy"></i> Scoreboard</a></li>
 @if(Auth::user()->isAdmin())
                        <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="fas fa-cog"></i> Admin Panel</a></li>
                        @endif
                        @if(Auth::user()->isExamManager())
                        <li><a class="dropdown-item" href="{{ route('exam-manager.dashboard') }}"><i class="fas fa-play-circle"></i> Exam Manager</a></li>
                        @endif
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
            @endauth
        </div>
    </nav>

    <main class="container mt-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    @stack('scripts')
</body>
</html>
