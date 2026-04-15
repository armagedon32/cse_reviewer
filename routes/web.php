<?php

use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Student\PaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');

Route::middleware('guest')->group(function (): void {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);

    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/student/dashboard', [DashboardController::class, 'student'])
        ->middleware('role:student')
        ->name('student.dashboard');
    Route::get('/student/exam', [DashboardController::class, 'exam'])
        ->middleware('role:student')
        ->name('student.exam');
    Route::post('/student/exam', [DashboardController::class, 'submitExam'])
        ->middleware('role:student')
        ->name('student.exam.submit');
    Route::get('/student/exam/history', [DashboardController::class, 'examHistory'])
        ->middleware('role:student')
        ->name('student.exam.history');
    Route::get('/student/exam/review/{examAttempt}', [DashboardController::class, 'reviewExam'])
        ->middleware('role:student')
        ->name('student.exam.review');
    Route::get('/student/payment-renewal', [PaymentController::class, 'edit'])
        ->middleware('role:student')
        ->name('student.payment.edit');
    Route::post('/student/payment-renewal', [PaymentController::class, 'update'])
        ->middleware('role:student')
        ->name('student.payment.update');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');
    Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
    Route::post('/users/{user}/reset-password', [AdminUserController::class, 'resetPassword'])->name('admin.users.reset-password');
    Route::get('/users/create-admin', [AdminUserController::class, 'create'])->name('admin.users.create');
    Route::post('/users/create-admin', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::get('/payments', [AdminUserController::class, 'payments'])->name('admin.payments.index');
    Route::patch('/payments/{user}/approve', [AdminUserController::class, 'approvePayment'])->name('admin.payments.approve');
    Route::patch('/payments/{user}/reject', [AdminUserController::class, 'rejectPayment'])->name('admin.payments.reject');
    Route::get('/payments/{user}/receipt', [AdminUserController::class, 'receipt'])->name('admin.payments.receipt');
    Route::get('/questions', [QuestionController::class, 'index'])->name('admin.questions.index');
    Route::get('/questions/export', [QuestionController::class, 'exportAll'])->name('admin.questions.export');
    Route::get('/questions/sample-csv', [QuestionController::class, 'downloadSample'])->name('admin.questions.sample');
    Route::get('/questions/preview', [QuestionController::class, 'preview'])->name('admin.questions.preview');
    Route::get('/questions/{question}/edit', [QuestionController::class, 'edit'])->name('admin.questions.edit');
    Route::post('/questions', [QuestionController::class, 'store'])->name('admin.questions.store');
    Route::post('/questions/import', [QuestionController::class, 'import'])->name('admin.questions.import');
    Route::delete('/questions', [QuestionController::class, 'destroyAll'])->name('admin.questions.destroyAll');
    Route::delete('/questions/duplicates', [QuestionController::class, 'destroyDuplicates'])->name('admin.questions.destroyDuplicates');
    Route::patch('/questions/encoding-fixes', [QuestionController::class, 'fixEncodingIssues'])->name('admin.questions.fixEncoding');
    Route::put('/questions/{question}', [QuestionController::class, 'update'])->name('admin.questions.update');
    Route::delete('/questions/{question}', [QuestionController::class, 'destroy'])->name('admin.questions.destroy');
});
