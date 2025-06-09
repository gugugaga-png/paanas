<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\Teacher\SegmentController;
use App\Http\Controllers\Teacher\MailController;
use App\Http\Controllers\Student\TransactionController; // ✅ Tambahkan ini
use Illuminate\Support\Facades\Auth;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('welcome');


// Grup rute untuk profil pengguna yang terautentikasi
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/editprofile', [ProfileController::class, 'edit'])->name('profile.editprofile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

require __DIR__.'/auth.php';

// Grup rute untuk peran Guru
Route::middleware(['auth', 'verified', 'is_teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', [TeacherController::class, 'dashboard'])->name('dashboard');

    Route::get('/segments/create', [SegmentController::class, 'create'])->name('segments.create');
    Route::post('/segments', [SegmentController::class, 'store'])->name('segments.store');
    Route::get('/segments/{segment}', [SegmentController::class, 'show'])->name('segments.show');
    Route::get('/segments/{segment}/students', [SegmentController::class, 'showStudents'])->name('segments.students');

    Route::get('/mail', [MailController::class, 'index'])->name('mail.index');
    Route::get('/segments/{segment}/mail', [MailController::class, 'indexBySegment'])->name('segments.mail.index');

    // --- PERBAIKAN PENTING DI SINI ---
    Route::post('/transactions/{transaction}/approve', [TeacherController::class, 'approveTransaction'])->name('transactions.approve');
    Route::post('/transactions/{transaction}/reject', [TeacherController::class, 'rejectTransaction'])->name('transactions.reject');
    // --- AKHIR PERBAIKAN PENTING ---
});
// Grup rute untuk peran Siswa
// routes/web.php
// routes/web.php

// ... (rute lainnya) ...

Route::middleware(['auth', 'is_student'])->group(function () {
    Route::get('/student/dashboard', [App\Http\Controllers\StudentController::class, 'dashboard'])->name('student.dashboard');
    Route::get('/student/join-segment-form', [App\Http\Controllers\StudentController::class, 'joinSegmentForm'])->name('student.join_segment_form');
    Route::post('/student/join-segment', [App\Http\Controllers\StudentController::class, 'joinSegment'])->name('student.join_segment');

    // Rute untuk menampilkan semua segmen  
    Route::get('/student/segments/join', [StudentController::class, 'showJoinSegmentForm'])->name('student.join.segment.form');
    Route::get('/student/segments', [App\Http\Controllers\StudentController::class, 'indexSegments'])->name('student.segments.index');

    Route::get('/student/segments/{segment}', [App\Http\Controllers\StudentController::class, 'segmentDetail'])->name('student.segment.detail');

    Route::get('/student/deposit', [App\Http\Controllers\StudentController::class, 'depositForm'])->name('student.deposit.form');
    Route::post('/student/deposit', [App\Http\Controllers\StudentController::class, 'submitDeposit'])->name('student.deposit.store');
    Route::get('/student/withdraw', [App\Http\Controllers\StudentController::class, 'withdrawForm'])->name('student.withdraw.form');
   Route::post('/student/withdraw', 'App\Http\Controllers\StudentController@submitWithdraw')->name('student.withdraw.store');
});

// ... (rute lainnya) ...
// Logika pengalihan dashboard utama (menggunakan role_id langsung)
Route::get('/dashboard', function () {
    $user = auth()->user();

    // Langsung cek role_id
    if ($user->role_id === 2) { // Asumsi 2 adalah ID untuk 'guru'
        return redirect()->route('teacher.dashboard');
    } elseif ($user->role_id === 3) { // Asumsi 3 adalah ID untuk 'murid'
        return redirect()->route('student.dashboard');
    }

    // Fallback untuk peran yang tidak diharapkan (keluar dan mengarahkan ke beranda)
    Auth::logout();
    return redirect('/')->with('error', 'Akun Anda tidak memiliki peran yang valid.');
})->middleware(['auth', 'verified'])->name('dashboard');