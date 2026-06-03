<?php

use App\Http\Controllers\Admin\ChapterController as AdminChapterController;
use App\Http\Controllers\Admin\ModuleController as AdminModuleController;
use App\Http\Controllers\Admin\SemesterController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Student\AttemptController as StudentAttemptController;
use App\Http\Controllers\Student\EvaluationController as StudentEvaluationController;
use App\Http\Controllers\Student\ResultController as StudentResultController;
use App\Http\Controllers\Teacher\EvaluationController as TeacherEvaluationController;
use App\Http\Controllers\Teacher\QuestionController as TeacherQuestionController;
use App\Http\Controllers\Teacher\StatisticsController as TeacherStatsController;
use Illuminate\Support\Facades\Route;

// ---------- Public / Auth ----------
Route::get('/', fn () => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register',  [RegisterController::class, 'showRegister'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')->name('logout');

// ---------- Authenticated ----------
Route::middleware('auth')->group(function () {

    // Generic dashboard dispatcher — redirects based on role
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ---------- ADMIN ----------
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [DashboardController::class, 'admin'])->name('dashboard');

        Route::resource('users',     UserController::class);
        Route::resource('semesters', SemesterController::class);
        Route::resource('modules',   AdminModuleController::class);

        // Chapters are nested under a module
        Route::resource('modules.chapters', AdminChapterController::class)
             ->shallow();

        // Quick action: enroll/unenroll students
        Route::post('modules/{module}/enroll',   [AdminModuleController::class, 'enroll'])->name('modules.enroll');
        Route::post('modules/{module}/unenroll', [AdminModuleController::class, 'unenroll'])->name('modules.unenroll');
    });

    // ---------- TEACHER ----------
    Route::middleware('role:teacher')->prefix('teacher')->name('teacher.')->group(function () {
        Route::get('/', [DashboardController::class, 'teacher'])->name('dashboard');

        Route::resource('evaluations', TeacherEvaluationController::class);

        Route::post('evaluations/{evaluation}/publish', [TeacherEvaluationController::class, 'publish'])->name('evaluations.publish');
        Route::post('evaluations/{evaluation}/close',   [TeacherEvaluationController::class, 'close'])->name('evaluations.close');
        Route::post('evaluations/{evaluation}/duplicate', [TeacherEvaluationController::class, 'duplicate'])->name('evaluations.duplicate');

        // Nested questions
        Route::resource('evaluations.questions', TeacherQuestionController::class)->shallow();

        // Statistics
        Route::get('statistics',                       [TeacherStatsController::class, 'index'])->name('statistics.index');
        Route::get('statistics/{evaluation}',          [TeacherStatsController::class, 'show'])->name('statistics.show');
        Route::get('statistics/module/{module}',       [TeacherStatsController::class, 'module'])->name('statistics.module');
    });

    // ---------- STUDENT ----------
    Route::middleware('role:student')->prefix('student')->name('student.')->group(function () {
        Route::get('/', [DashboardController::class, 'student'])->name('dashboard');

        Route::get('evaluations',              [StudentEvaluationController::class, 'index'])->name('evaluations.index');
        Route::get('evaluations/{evaluation}', [StudentEvaluationController::class, 'show'])->name('evaluations.show');

        // Attempts
        Route::post('evaluations/{evaluation}/start',     [StudentAttemptController::class, 'start'])->name('attempts.start');
        Route::get ('attempts/{attempt}/take',            [StudentAttemptController::class, 'take'])->name('attempts.take');
        Route::post('attempts/{attempt}/submit',          [StudentAttemptController::class, 'submit'])->name('attempts.submit');

        // Results
        Route::get('results',                  [StudentResultController::class, 'index'])->name('results.index');
        Route::get('results/{attempt}',        [StudentResultController::class, 'show'])->name('results.show');
    });
});
