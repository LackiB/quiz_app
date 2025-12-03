<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Answer; // Dodaj import dla modelu Answer
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB; // Dodaj import dla transakcji

class QuestionCrudController extends Controller
{
    /**
     * Wyświetla listę pytań dla konkretnego quizu.
     */
    public function index(Quiz $quiz): View
    {
        // Lazy loading relacji 'answers'
        $quiz->load('questions.answers');
        
        return view('admin.questions.index', [
            'quiz' => $quiz,
            'questions' => $quiz->questions()->orderBy('id')->get(),
        ]);
    }

    /**
     * Pokazuje formularz do tworzenia nowego pytania.
     */
    public function create(Quiz $quiz): View
    {
        return view('admin.questions.create', compact('quiz'));
    }

    /**
     * Zapisuje nowe pytanie i jego odpowiedzi.
     */
    public function store(Request $request, Quiz $quiz): RedirectResponse
    {
        // 1. Walidacja (Temat 6)
        $validated = $request->validate([
            'question_text' => 'required|string|min:3',
            // Muszą być co najmniej 2 odpowiedzi, a każda musi być stringiem
            'answers' => 'required|array|min:2', 
            'answers.*.answer_text' => 'required|string|min:1',
            // Poprawna odpowiedź musi być indeksem istniejącej odpowiedzi
            'correct_answer_index' => 'required|integer|min:0|max:' . (count($request->answers) - 1),
        ]);
        
        // 2. Transakcja (Temat 7)
        // Zapewnia, że jeśli coś pójdzie nie tak z odpowiedziami, pytanie też nie zostanie zapisane.
        DB::beginTransaction();

        try {
            // 3. Tworzenie Pytania (Temat 4)
            $question = $quiz->questions()->create([
                'question_text' => $validated['question_text'],
            ]);
            
            // 4. Tworzenie Odpowiedzi (Temat 9)
            $answersData = [];
            foreach ($validated['answers'] as $index => $answerData) {
                $isCorrect = ($index == $validated['correct_answer_index']);
                
                $answersData[] = new Answer([
                    'answer_text' => $answerData['answer_text'],
                    'is_correct' => $isCorrect,
                ]);
            }
            
            // Masowe zapisywanie relacji hasMany
            $question->answers()->saveMany($answersData);

            DB::commit();

            return redirect()
                ->route('admin.quizzes.questions.index', $quiz)
                ->with('success', 'Pytanie i odpowiedzi zostały pomyślnie dodane!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Opcjonalnie: Zaloguj błąd ($e->getMessage())
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Wystąpił błąd podczas dodawania pytania i odpowiedzi.');
        }
    }
    
    /**
     * Wyświetla formularz do edycji pytania.
     * Używamy Route Model Binding dla Quiz i Question.
     */
    public function edit(Quiz $quiz, Question $question): View
    {
        // Sprawdź, czy pytanie należy do tego quizu
        if ($question->quiz_id !== $quiz->id) {
            abort(404);
        }
        
        // Załaduj odpowiedzi pytania, by wyświetlić je w formularzu
        $question->load('answers');

        // Ustal, który indeks odpowiedzi jest poprawny
        $correctAnswerIndex = $question->answers->search(function ($answer) {
            return $answer->is_correct;
        });

        return view('admin.questions.edit', [
            'quiz' => $quiz,
            'question' => $question,
            'correctAnswerIndex' => $correctAnswerIndex !== false ? $correctAnswerIndex : 0,
        ]);
    }

    /**
     * Aktualizuje pytanie i jego odpowiedzi.
     */
    public function update(Request $request, Quiz $quiz, Question $question): RedirectResponse
    {
        // 1. Walidacja (jak przy tworzeniu)
        $validated = $request->validate([
            'question_text' => 'required|string|min:3',
            'answers' => 'required|array|min:2', 
            'answers.*.answer_text' => 'required|string|min:1',
            'correct_answer_index' => 'required|integer|min:0|max:' . (count($request->answers) - 1),
        ]);
        
        // 2. Transakcja
        DB::beginTransaction();

        try {
            // 3. Aktualizacja Pytania
            $question->update(['question_text' => $validated['question_text']]);

            // 4. Aktualizacja Odpowiedzi
            $currentAnswers = $question->answers;
            
            foreach ($validated['answers'] as $index => $answerData) {
                $isCorrect = ($index == $validated['correct_answer_index']);
                
                // Użyj istniejącej odpowiedzi na podstawie indeksu lub utwórz nową, jeśli dodano pola
                if (isset($currentAnswers[$index])) {
                    $currentAnswers[$index]->update([
                        'answer_text' => $answerData['answer_text'],
                        'is_correct' => $isCorrect,
                    ]);
                } else {
                    // Jeśli chcemy, by formularz dodawał nowe odpowiedzi, musimy je tutaj utworzyć.
                    // Na razie zakładamy stałą liczbę 4 odpowiedzi.
                }
            }
            
            DB::commit();

            return redirect()
                ->route('admin.quizzes.questions.index', $quiz)
                ->with('success', 'Pytanie zostało pomyślnie zaktualizowane!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Wystąpił błąd podczas aktualizacji pytania.');
        }
    }

    /**
     * Usuwa pytanie i wszystkie jego odpowiedzi (dzięki onDelete('cascade') w migracji).
     */
    public function destroy(Quiz $quiz, Question $question): RedirectResponse
    {
        // Sprawdzenie przynależności jest zawsze dobrą praktyką
        if ($question->quiz_id !== $quiz->id) {
            abort(404);
        }
        
        $question->delete();

        return redirect()
            ->route('admin.quizzes.questions.index', $quiz)
            ->with('success', 'Pytanie zostało pomyślnie usunięte.');
    }
}