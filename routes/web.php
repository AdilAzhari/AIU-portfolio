<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CredentialController;
use App\Http\Controllers\PublicVerificationController;
use App\Http\Controllers\EvidenceController;
use App\Http\Controllers\Issuer\DashboardController as IssuerDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Verifier\DashboardController as VerifierDashboardController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

Route::get('/', function () {

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified','redirect.role'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {

    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/admin/logs', [AdminController::class, 'logs'])->name('admin.logs');
        Route::get('/admin/logs/export', [AdminController::class, 'exportLogs'])->name('admin.logs.export');
    });

    Route::middleware('role:issuer')->group(function () {
        Route::get('/issuer/dashboard', [IssuerDashboardController::class, 'index'])->name('issuer.dashboard');
    });

    Route::middleware('role:student')->group(function () {
        Route::get('/student/dashboard', [StudentDashboardController::class, 'index'])->name('student.dashboard');
    });

    Route::middleware('role:verifier')->group(function () {
        Route::get('/verifier/dashboard', [VerifierDashboardController::class, 'index'])->name('verifier.dashboard');
    });
});

Route::middleware(['auth', 'throttle:10,1'])->group(function (): void {
    Route::get('/evidence/create', [EvidenceController::class, 'create'])->name('evidence.create');
    Route::post('/evidence', [EvidenceController::class, 'store'])->name('evidence.store');
});

Route::middleware(['auth'])->group(function (): void {
    Route::get('/credentials/{credential}/qr', [QrCodeController::class, 'generate'])->name('credentials.qr');
});

Route::middleware(['auth', 'role:issuer'])->group(function (): void {
    Route::get('/credentials/create', [CredentialController::class, 'create'])->name('credentials.create');
    Route::post('/credentials', [CredentialController::class, 'store'])->name('credentials.store');
    Route::patch('/credentials/{credential}/issue', [CredentialController::class, 'issue'])->name('credentials.issue');
    Route::patch('/credentials/{credential}/revoke', [CredentialController::class, 'revoke'])->name('credentials.revoke');
});

Route::get('/verify/{credential}', [PublicVerificationController::class, 'show'])
    ->name('verify.show');

require __DIR__.'/auth.php';
