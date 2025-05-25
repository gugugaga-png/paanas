<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Auth; // Make sure this is imported

Route::get('/', function () {
    return view('welcome');
});

// Make sure the conflicting default /dashboard route is DELETED, not just commented out.

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::middleware(['auth', 'verified', 'is_teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', [TeacherController::class, 'dashboard'])->name('dashboard');
    Route::get('/segments/create', [TeacherController::class, 'createSegment'])->name('segments.create');
    Route::post('/segments', [TeacherController::class, 'storeSegment'])->name('segments.store');
    Route::get('/segments/{segment}/transactions', [TeacherController::class, 'showSegmentTransactions'])->name('segments.transactions');

    Route::post('/transactions/{transaction}/approve', [TeacherController::class, 'approveTransaction'])->name('transactions.approve');
    Route::post('/transactions/{transaction}/reject', [TeacherController::class, 'rejectTransaction'])->name('transactions.reject');
});

Route::middleware(['auth', 'verified', 'is_student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
    Route::get('/join-segment', [StudentController::class, 'joinSegmentForm'])->name('join_segment_form');
    Route::post('/join-segment', [StudentController::class, 'joinSegment'])->name('join_segment');
     Route::get('/deposit', [App\Http\Controllers\StudentController::class, 'depositForm'])->name('deposit.form');
    Route::post('/deposit', [App\Http\Controllers\StudentController::class, 'submitDeposit'])->name('submit_deposit'); // You'll implement this next!
});

// The main dashboard redirection logic (using role_id directly)
Route::get('/dashboard', function () {
    $user = auth()->user();

    // Directly check role_id
    if ($user->role_id === 2) { // Assuming 2 is the ID for 'guru'
        return redirect()->route('teacher.dashboard');
    } elseif ($user->role_id === 3) { // Assuming 3 is the ID for 'murid'
        return redirect()->route('student.dashboard');
    }

    // Fallback for unexpected roles (logs out and redirects to home)
    Auth::logout();
    return redirect('/')->with('error', 'Akun Anda tidak memiliki peran yang valid.');
})->middleware(['auth', 'verified'])->name('dashboard');