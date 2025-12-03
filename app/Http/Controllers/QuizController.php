<?php

namespace App\Http\Controllers;

use App\Models\Quiz; // Pamiętaj o zaimportowaniu modelu!
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Question;
use App\Models\Answer;
use Illuminate\Support\Facades\Session; // Konieczny do zarządzania sesją


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
        // Sprawdź, czy pytanie należy do tego quizu
        if ($question->quiz_id !== $quiz->id) {
            abort(404); // Pytanie nie pasuje do quizu
        }

        // Przekazujemy dane do widoku
        return view('quiz.question', [
            'quiz' => $quiz,
            'question' => $question,
            'answers' => $question->answers->shuffle(), // Losowa kolejność odpowiedzi
        ]);
    }

        
    
    public function submitAnswer(Request $request)
    {
        // 1. WALIDACJA DANYCH (Temat 6)
        // Sprawdzamy, czy użytkownik wybrał odpowiedź (answer_id)
        $validated = $request->validate([
            'quiz_id' => 'required|exists:quizzes,id', // Upewnij się, że quiz_id istnieje
            'question_id' => 'required|exists:questions,id', // Upewnij się, że question_id istnieje
            'answer_id' => 'required|exists:answers,id', // Odpowiedź jest wymagana i musi istnieć
        ]);

        $currentQuizId = $validated['quiz_id'];
        $currentQuestionId = $validated['question_id'];
        $submittedAnswerId = $validated['answer_id'];

        // 2. LOGIKA SPRAWDZANIA POPRAWNOŚCI
        
        // Znajdź poprawną odpowiedź i sprawdzamy, czy przesłana odpowiedź jest poprawna
        $correctAnswer = Answer::where('question_id', $currentQuestionId)
                               ->where('is_correct', true)
                               ->firstOrFail();

        $isCorrect = ($submittedAnswerId == $correctAnswer->id);

        // 3. ZARZĄDZANIE SESJĄ (Śledzenie wyników)

        // Pobierz aktualny stan quizu z sesji lub utwórz nowy
        $quizProgress = Session::get('quiz_progress.' . $currentQuizId, [
            'score' => 0, 
            'answered_questions' => [], 
            'last_question_id' => 0
        ]);
        
        // Zabezpieczenie: jeśli na to pytanie już odpowiedzieliśmy, ignorujemy to żądanie
        if (in_array($currentQuestionId, $quizProgress['answered_questions'])) {
             return $this->redirectToNextQuestion($currentQuizId);
        }

        // Aktualizacja stanu:
        if ($isCorrect) {
            $quizProgress['score']++;
        }

        $quizProgress['answered_questions'][] = $currentQuestionId;
        $quizProgress['last_question_id'] = $currentQuestionId;

        // Zapisz zaktualizowany stan w sesji
        Session::put('quiz_progress.' . $currentQuizId, $quizProgress);

        // 4. PRZEKIEROWANIE DO KOLEJNEGO PYTANIA
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

        // Znajdź następne pytanie w sekwencji, które NIE zostało jeszcze wyświetlone
        $nextQuestion = $quiz->questions()
                             ->where('id', '>', $lastQuestionId)
                             ->orderBy('id')
                             ->first();
        
        if ($nextQuestion) {
            // Przekieruj do metody showQuestion() z nowym ID
            // Uwaga: musimy zmodyfikować showQuestion, by akceptowało questionId lub użyć redirect route.
            
            // Najprościej: przekieruj do tej samej trasy startowej, ale z nowym ID pytania
            // To wymaga modyfikacji metody showQuestion (patrz niżej)
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

        // Opcjonalnie: usuń dane quizu z sesji po wyświetleniu wyników
        Session::forget('quiz_progress.' . $quiz->id);

        return view('quiz.results', [
            'quiz' => $quiz,
            'score' => $quizProgress['score'],
            'total' => $totalQuestions,
        ]);
    }
}