<?php

namespace App\Http\Controllers;

use App\Models\Quiz; // Pamiętaj o zaimportowaniu modelu!
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuizController extends Controller
{
    /**
     * Wyświetla listę wszystkich dostępnych quizów.
     */
    public function index(): View
    {
        // 1. Pobierz wszystkie quizy z bazy danych
        $quizzes = Quiz::all(); 

        // 2. Przekaż pobrane dane ($quizzes) do widoku 'dashboard'
        return view('dashboard', [
            'quizzes' => $quizzes,
        ]);
    }
}