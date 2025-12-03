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
    
    // Na razie pomijamy show, edit, update, destroy – są podobne, ale bardziej złożone
    // z uwagi na konieczność modyfikacji odpowiedzi.
    
    // ... pozostałe metody CRUD ...
}