<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
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
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {

    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', fn () => Inertia::render('Admin/Dashboard'));
    });

    Route::middleware('role:issuer')->group(function () {
        Route::get('/issuer/dashboard', fn () => Inertia::render('Issuer/Dashboard'));
    });

    Route::middleware('role:student')->group(function () {
        Route::get('/student/dashboard', fn () => Inertia::render('Student/Dashboard'));
    });

    Route::middleware('role:verifier')->group(function () {
        Route::get('/verifier/dashboard', fn () => Inertia::render('Verifier/Dashboard'));
    });
});


require __DIR__.'/auth.php';
