<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ExamManagerController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ScoreboardController;

// Auth routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Dashboard/Welcome page
    Route::get('/dashboard', [QuizController::class, 'showDashboard'])->name('dashboard');
    
    // Quiz interface
    Route::get('/quiz', [QuizController::class, 'showQuiz'])->name('quiz');
    Route::post('/quiz/answer', [QuizController::class, 'submitAnswer'])->name('quiz.answer');
    Route::get('/quiz/waiting', [QuizController::class, 'showWaiting'])->name('quiz.waiting');
    
    // Real-time scoreboard
    Route::get('/scoreboard', [ScoreboardController::class, 'showScoreboard'])->name('scoreboard');
    Route::get('/scoreboard/live', [ScoreboardController::class, 'getLiveScoreboard'])->name('scoreboard.live');
    
    // Admin routes
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
        Route::get('/admin/questions', [AdminController::class, 'showQuestions'])->name('admin.questions');
        Route::post('/admin/questions', [AdminController::class, 'storeQuestion'])->name('admin.questions.store');
        Route::get('/admin/questions/create', [AdminController::class, 'createQuestion'])->name('admin.questions.create');
        Route::get('/admin/questions/{question}/edit', [AdminController::class, 'editQuestion'])->name('admin.questions.edit');
        Route::put('/admin/questions/{question}', [AdminController::class, 'updateQuestion'])->name('admin.questions.update');
        Route::delete('/admin/questions/{question}', [AdminController::class, 'deleteQuestion'])->name('admin.questions.delete');
        Route::post('/admin/questions/import', [AdminController::class, 'importQuestions'])->name('admin.questions.import');
    });
    
    // Exam Manager routes
    Route::middleware(['role:exam_manager'])->group(function () {
        Route::get('/exam-manager', [ExamManagerController::class, 'index'])->name('exam-manager.dashboard');
        Route::post('/exam-manager/start-test', [ExamManagerController::class, 'startTest'])->name('exam-manager.start-test');
        Route::post('/exam-manager/next-question', [ExamManagerController::class, 'nextQuestion'])->name('exam-manager.next-question');
        Route::post('/exam-manager/end-test', [ExamManagerController::class, 'endTest'])->name('exam-manager.end-test');
        Route::get('/exam-manager/users-status', [ExamManagerController::class, 'showUsersStatus'])->name('exam-manager.users-status');
    });
});