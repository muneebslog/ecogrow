<?php

use App\Http\Controllers\Onboarding\QuizController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('onboarding/quiz', [QuizController::class, 'show'])->name('onboarding.quiz');
    Route::post('onboarding/quiz', [QuizController::class, 'submit'])->name('onboarding.quiz.submit');
});
