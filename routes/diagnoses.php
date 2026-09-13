<?php

use App\Http\Controllers\DiagnosisController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('diagnoses/create', [DiagnosisController::class, 'create'])->name('diagnoses.create');
    Route::post('diagnoses', [DiagnosisController::class, 'store'])->name('diagnoses.store');
    Route::get('diagnoses/{diagnosis}', [DiagnosisController::class, 'show'])->name('diagnoses.show');
});
