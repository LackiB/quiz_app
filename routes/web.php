<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuizController; 
use App\Http\Controllers\Admin; // Import dla kontrolerów Admin

// Trasa domyślna - po instalacji Breeze kieruje do welcome
Route::get('/', function () {
    return view('welcome');
});

// Trasa dla uwierzytelnionych użytkowników
Route::middleware('auth')->group(function () {
    // Zmieniamy domyślną trasę 'dashboard', aby pokazywała listę quizów
    Route::get('/dashboard', [QuizController::class, 'index'])->name('dashboard'); 

    // FRONT-END QUIZU
    Route::get('/quiz/{quiz}/q/{question}', [QuizController::class, 'showQuestion'])->name('quiz.show_next');
    Route::post('/quiz/submit', [QuizController::class, 'submitAnswer'])->name('quiz.submit');
    Route::get('/quiz/{quiz}/results', [QuizController::class, 'showResults'])->name('quiz.results');

    // Trasa do profilu
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// BLOK TRAS ADMINA (BACK-END)
Route::middleware(['auth', 'can:access-admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // TRASA GŁÓWNA ADMINA: /admin (nazwa: admin.dashboard)
    Route::get('/', function () {
        return view('admin.dashboard'); 
    })->name('dashboard'); 

    // CRUD QUIZÓW: /admin/quizzes
    Route::resource('quizzes', Admin\QuizCrudController::class); 

    // CRUD PYTAŃ (ZAGNIEŻDŻONY): /admin/quizzes/{quiz}/questions
    Route::resource('quizzes.questions', Admin\QuestionCrudController::class)->except(['show']);
    
});

require __DIR__.'/auth.php';