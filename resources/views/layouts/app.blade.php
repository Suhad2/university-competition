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
        
        /* Notification styles */
        #update-notification {
            transition: opacity 0.3s ease-in-out;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        #update-notification .btn-close {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
        }
        
        /* Smooth transitions for UI elements */
        .answer-option {
            transition: all 0.2s ease-in-out;
        }
        
        .answer-option.selected {
            transform: scale(1.02);
            box-shadow: 0 2px 8px rgba(0, 123, 255, 0.3);
        }
        
        .timer-display {
            transition: color 0.3s ease;
        }
    </style>
    
    <!-- Pusher configuration -->
    <meta name="pusher-key" content="{{ env('PUSHER_APP_KEY', '17ec3014a90b3757e007') }}">
    <meta name="pusher-cluster" content="{{ env('PUSHER_APP_CLUSTER', 'mt1') }}">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">

            {{-- Brand --}}
            @auth
                @if (auth()->user()->isAdmin())
                    <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
                    @elseif (auth()->user()->isExamManager())
                        <a class="navbar-brand" href="{{ route('exam-manager.dashboard') }}">
                        @else
                            <a class="navbar-brand" href="{{ route('dashboard') }}">
                @endif
                <i class="fas fa-graduation-cap"></i> University Competition
                </a>
            @endauth

            @guest
                <a class="navbar-brand" href="{{ route('login') }}">
                    <i class="fas fa-graduation-cap"></i> University Competition
                </a>
            @endguest

            {{-- User Dropdown --}}
            @auth
                <div class="navbar-nav ms-auto">
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> {{ auth()->user()->name }}
                        </a>

                        <ul class="dropdown-menu">
                            <li>
                                <span class="dropdown-item-text">
                                    <strong>Role:</strong>
                                    {{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}
                                </span>
                            </li>

                            @if (auth()->user()->university)
                                <li>
                                    <span class="dropdown-item-text">
                                        <strong>University:</strong>
                                        {{ auth()->user()->university }}
                                    </span>
                                </li>
                            @endif

                            <li>
                                <hr class="dropdown-divider">
                            </li>

                            {{-- Admin --}}
                            @if (auth()->user()->isAdmin())
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                        <i class="fas fa-cog"></i> Admin Panel
                                    </a>
                                </li>

                                {{-- Exam Manager --}}
                            @elseif (auth()->user()->isExamManager())
                                <li>
                                    <a class="dropdown-item" href="{{ route('exam-manager.dashboard') }}">
                                        <i class="fas fa-play-circle"></i> Exam Manager
                                    </a>
                                </li>

                                {{-- Participant --}}
                            @else
                                <li>
                                    <a class="dropdown-item dashboard-link" href="{{ route('dashboard') }}">
                                        <i class="fas fa-user-cog"></i> Dashboard
                                    </a>
                                </li>
                            @endif


                            <li>
                                <a class="dropdown-item" href="{{ route('scoreboard') }}">
                                    <i class="fas fa-trophy"></i> Scoreboard
                                </a>
                            </li>

                            <li>
                                <hr class="dropdown-divider">
                            </li>

                            <li>
                                <form method="POST" action="{{ route('logout') }}">
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
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Global notification container -->
        <div id="update-notification" class="alert alert-info alert-dismissible fade show d-none" role="alert" style="position: fixed; top: 80px; right: 20px; z-index: 9999; max-width: 400px;">
            <i class="fas fa-bell"></i> <span id="notification-message"></span>
            <button type="button" class="btn-close" onclick="hideNotification()"></button>
        </div>

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    
    <!-- Pusher and Echo from CDN - Using stable versions -->
    <script src="https://cdn.jsdelivr.net/npm/pusher-js@7.2.0/dist/web/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.3/dist/echo.iife.js"></script>
    
    <script>
      
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: document.querySelector('meta[name="pusher-key"]')?.getAttribute('content') || '17ec3014a90b3757e007',
            cluster: document.querySelector('meta[name="pusher-cluster"]')?.getAttribute('content') || 'mt1',
            forceTLS: true,
            enableLogging: true
        });
        
        console.log('Echo initialized from CDN:', window.Echo);
        
        // Global notification system
        function showNotification(message) {
            const notification = document.getElementById('update-notification');
            const notificationMessage = document.getElementById('notification-message');
            
            if (notification && notificationMessage) {
                notificationMessage.textContent = message;
                notification.classList.remove('d-none');
                
                // Auto-hide after 5 seconds
                setTimeout(function() {
                    hideNotification();
                }, 5000);
            }
        }

        function hideNotification() {
            const notification = document.getElementById('update-notification');
            if (notification) {
                notification.classList.add('d-none');
            }
        }
    </script>
    
    @stack('scripts')
</body>

</html>
