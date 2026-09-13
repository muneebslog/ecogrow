<?php

use App\Http\Controllers\RewardsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('rewards', [RewardsController::class, 'index'])->name('rewards.index');
});
