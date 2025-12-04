<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CredentialController;
use App\Http\Controllers\EvidenceController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {

    $qr = base64_encode(
        QrCode::format('png')
            ->size(200)
            ->generate(route('verify.show', $credential->id))
    );

    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified', 'redirect.role'])->name('dashboard');

Route::middleware('auth')->group(function (): void {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->group(function (): void {

    Route::middleware('role:admin')->group(function (): void {
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/admin/logs', [AdminController::class, 'logs'])->name('admin.logs');
        Route::get('/admin/logs/export', [AdminController::class, 'exportLogs'])->name('admin.logs.export');
    });

    Route::middleware('role:issuer')->group(function (): void {
        Route::get('/issuer/dashboard', fn () => Inertia::render('Issuer/Dashboard'))->name('issuer.dashboard');
    });

    Route::middleware('role:student')->group(function (): void {
        Route::get('/student/dashboard', fn () => Inertia::render('Student/Dashboard'))->name('student.dashboard');
    });

    Route::middleware('role:verifier')->group(function (): void {
        Route::get('/verifier/dashboard', fn () => Inertia::render('Verifier/Dashboard'))->name('verifier.dashboard');
    });
});

Route::middleware(['auth','throttle:10,1'])->group(function (): void {
    Route::post('/evidence', [EvidenceController::class, 'store'])->name('evidence.store');
});

Route::middleware(['auth', 'role:issuer'])->group(function (): void {
    Route::post('/credentials', [CredentialController::class, 'store'])->name('credentials.store');
    Route::patch('/credentials/{credential}/issue', [CredentialController::class, 'issue'])->name('credentials.issue');
    Route::patch('/credentials/{credential}/revoke', [CredentialController::class, 'revoke'])->name('credentials.revoke');
});

Route::get('/verify/{credential}', [PublicVerificationController::class, 'show'])
    ->name('verify.show');

require __DIR__.'/auth.php';
