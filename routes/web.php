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
});

// Grup rute untuk profil pengguna yang terautentikasi
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
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

    Route::post('/transactions/{transaction}/approve', [MailController::class, 'approve'])->name('transactions.approve');
    Route::post('/transactions/{transaction}/reject', [MailController::class, 'reject'])->name('transactions.reject');
});

// Grup rute untuk peran Siswa
Route::middleware(['auth', 'verified', 'is_student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
    Route::get('/join-segment', [StudentController::class, 'joinSegmentForm'])->name('join_segment_form');
    Route::post('/join-segment', [StudentController::class, 'joinSegment'])->name('join_segment');
    Route::get('/deposit', [StudentController::class, 'depositForm'])->name('deposit.form');
    
    // Sebelumnya: '/student/segment/{segment}/deposit'
    Route::get('/segment/{segment}/deposit', [TransactionController::class, 'create'])
        ->name('deposit.segment');
    
    Route::post('/deposit', [StudentController::class, 'submitDeposit'])->name('submit_deposit');
    
    // Sebelumnya: '/student/segments/{segment}'
    Route::get('/segments/{segment}', [StudentController::class, 'segmentDetail'])->name('segment.detail');
});


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