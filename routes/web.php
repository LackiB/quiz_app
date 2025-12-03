<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuizController; // Dodaj użycie kontrolera

// ... (pozostałe trasy, np. domyślna '/' i trasy uwierzytelniania)

// Trasa domyślna - po instalacji Breeze kieruje do dashboard
Route::get('/', function () {
    return view('welcome');
});

// Trasa dla uwierzytelnionych użytkowników
Route::middleware('auth')->group(function () {
    // Zmieniamy domyślną trasę 'dashboard', aby pokazywała listę quizów
    Route::get('/dashboard', [QuizController::class, 'index'])->name('dashboard'); 

    // NOWA TRASA: Uruchomienie quizu
    // Używamy parametru {quiz}, który zostanie przekazany do kontrolera
    Route::get('/quiz/{quiz}', [QuizController::class, 'showQuestion'])->name('quiz.start'); 
    
    // NOWA TRASA: Obsługa odpowiedzi (POST)
    Route::post('/quiz/submit', [QuizController::class, 'submitAnswer'])->name('quiz.submit');

    // Trasa do profilu
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
