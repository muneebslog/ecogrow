<?php

use App\Http\Controllers\PlantController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('plants', [PlantController::class, 'store'])->name('plants.store');
    Route::get('plants/{plant}', [PlantController::class, 'show'])->name('plants.show');
});
