<?php

use App\Http\Controllers\CareLogController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('plants/{plant}/care-logs', [CareLogController::class, 'store'])->name('care-logs.store');
});
