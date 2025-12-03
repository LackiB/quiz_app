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

    public function showQuestion(Quiz $quiz)
    {
        // Pobieramy pierwsze pytanie quizu z relacji
        // Używamy first() aby wziąć tylko jeden rekord
        $question = $quiz->questions()->first();

        // Jeżeli quiz nie ma pytań (co nie powinno się zdarzyć, ale to dobra praktyka)
        if (!$question) {
            return redirect()->route('dashboard')->with('error', 'Ten quiz nie zawiera pytań.');
        }

        // Przekazujemy quiz i pierwsze pytanie do widoku
        return view('quiz.question', [
            'quiz' => $quiz,
            'question' => $question,
            // Opcjonalnie: pobieramy odpowiedzi pytania, by je wyświetlić
            'answers' => $question->answers,
        ]);
    }
}