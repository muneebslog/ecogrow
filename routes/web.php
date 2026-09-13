<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

require __DIR__.'/settings.php';
require __DIR__.'/plants.php';
require __DIR__.'/onboarding.php';
require __DIR__.'/diagnoses.php';
require __DIR__.'/care-logs.php';
require __DIR__.'/rewards.php';
