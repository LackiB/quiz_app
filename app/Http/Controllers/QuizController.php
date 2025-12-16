<?php

namespace App\Http\Controllers;

use App\Models\Quiz; // Pamiętaj o zaimportowaniu modelu!
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Question;
use App\Models\Answer;
use Illuminate\Support\Facades\Session; // Konieczny do zarządzania sesją
use App\Models\Result;

class QuizController extends Controller
{
    /**
     * Wyświetla listę wszystkich dostępnych quizów.
     */

    public function index(): View
    {

        $quizzes = Quiz::all(); 

        foreach ($quizzes as $quiz) {
            // Dodajemy ID pierwszego pytania do obiektu quizu, aby użyć go w widoku
            $quiz->first_question_id = $quiz->questions()->orderBy('id')->first()->id ?? null;
        }

        return view('dashboard', [
            'quizzes' => $quizzes,
        ]);
    }

    public function showQuestion(Quiz $quiz, Question $question): View
    {
        // 1. Sprawdź, czy pytanie należy do tego quizu
        if ($question->quiz_id !== $quiz->id) {
            abort(404);
        }

        // 2. OBLICZANIE NUMERU PYTANIA
        // a) Pobierz listę wszystkich pytań dla tego quizu, posortowanych według ID (jak są wyświetlane)
        $allQuestions = $quiz->questions()->orderBy('id')->get();
        
        // b) Znajdź indeks (pozycję) aktualnego pytania w tej kolekcji
        // Index jest liczony od 0. Używamy 'id' i $question->id do wyszukania.
        $questionIndex = $allQuestions->search(function ($item) use ($question) {
            return $item->id === $question->id;
        });
        
        // c) Numer pytania dla użytkownika (Index + 1)
        $questionNumber = $questionIndex !== false ? $questionIndex + 1 : 1;
        
        // d) Całkowita liczba pytań
        $totalQuestions = $allQuestions->count();

        // 3. Przekazanie nowych zmiennych do widoku
        return view('quiz.question', [
            'quiz' => $quiz,
            'question' => $question,
            'answers' => $question->answers->shuffle(),
            'questionNumber' => $questionNumber,     // NOWA ZMIENNA
            'totalQuestions' => $totalQuestions,     // NOWA ZMIENNA
        ]);
    }

        
    
    public function submitAnswer(Request $request)
    {

        // Sprawdza, czy użytkownik wybrał odpowiedź (answer_id)
        $validated = $request->validate([
            'quiz_id' => 'required|exists:quizzes,id', 
            'question_id' => 'required|exists:questions,id', 
            'answer_id' => 'required|exists:answers,id', 
        ]);

        $currentQuizId = $validated['quiz_id'];
        $currentQuestionId = $validated['question_id'];
        $submittedAnswerId = $validated['answer_id'];

        
        
        
        $correctAnswer = Answer::where('question_id', $currentQuestionId)
                               ->where('is_correct', true)
                               ->firstOrFail();

        $isCorrect = ($submittedAnswerId == $correctAnswer->id);


        // Pobiera aktualny stan quizu z sesji lub utwórz nowy
        $quizProgress = Session::get('quiz_progress.' . $currentQuizId, [
            'score' => 0, 
            'answered_questions' => [], 
            'last_question_id' => 0
        ]);
        
        
        if (in_array($currentQuestionId, $quizProgress['answered_questions'])) {
             return $this->redirectToNextQuestion($currentQuizId);
        }

        
        if ($isCorrect) {
            $quizProgress['score']++;
        }

        $quizProgress['answered_questions'][] = $currentQuestionId;
        $quizProgress['last_question_id'] = $currentQuestionId;

       
        Session::put('quiz_progress.' . $currentQuizId, $quizProgress);

        
        return $this->redirectToNextQuestion($currentQuizId);
    }
    
    /**
     * Prywatna metoda pomocnicza do określania, co jest następnym krokiem.
     */
    private function redirectToNextQuestion(int $quizId)
    {
        $quiz = Quiz::findOrFail($quizId);
        $quizProgress = Session::get('quiz_progress.' . $quizId);
        $lastQuestionId = $quizProgress['last_question_id'];

        
        $nextQuestion = $quiz->questions()
                             ->where('id', '>', $lastQuestionId)
                             ->orderBy('id')
                             ->first();
        
        if ($nextQuestion) {
            
            return redirect()->route('quiz.show_next', [
                'quiz' => $quizId, 
                'question' => $nextQuestion->id
            ]);
            
        } else {
            // BRAK KOLEJNEGO PYTANIA - KONIEC QUIZU
            return redirect()->route('quiz.results', ['quiz' => $quizId]);
        }
    }

    public function showResults(Quiz $quiz): View
    {
        
        $quizProgress = Session::get('quiz_progress.' . $quiz->id, ['score' => 0, 'answered_questions' => []]);
        $totalQuestions = $quiz->questions()->count();
        $userScore = $quizProgress['score'];

        
        if (auth()->check()) { 
            
           
            Result::create([
                'user_id' => auth()->id(),
                'quiz_id' => $quiz->id,
                'score' => $userScore,
                'total_questions' => $totalQuestions,
            ]);
            
           
        }
        
        // USUNIĘCIE SESJI
        Session::forget('quiz_progress.' . $quiz->id);

       
        return view('quiz.results', [
            'quiz' => $quiz,
            'score' => $userScore,
            'total' => $totalQuestions,
        ]);
    }
}