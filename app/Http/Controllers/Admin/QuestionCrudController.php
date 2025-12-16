<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Answer; 
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB; 

class QuestionCrudController extends Controller
{
    
    public function index(Quiz $quiz): View
    {
        
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

    
    public function store(Request $request, Quiz $quiz): RedirectResponse
    {
        
        $validated = $request->validate([
            'question_text' => 'required|string|min:3',
            'answers' => 'required|array|min:2', 
            'answers.*.answer_text' => 'required|string|min:1',
            'correct_answer_index' => 'required|integer|min:0|max:' . (count($request->answers) - 1),
        ]);
        

        // Zapewnia, że jeśli coś pójdzie nie tak z odpowiedziami, pytanie też nie zostanie zapisane.
        DB::beginTransaction();

        try {
            
            $question = $quiz->questions()->create([
                'question_text' => $validated['question_text'],
            ]);
            
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
            

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Wystąpił błąd podczas dodawania pytania i odpowiedzi.');
        }
    }
    
    /**
     * Wyświetla formularz do edycji pytania.
     * Używa Route Model Binding dla Quiz i Question.
     */
    public function edit(Quiz $quiz, Question $question): View
    {
        if ($question->quiz_id !== $quiz->id) {
            abort(404);
        }
        
        $question->load('answers');


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

        $validated = $request->validate([
            'question_text' => 'required|string|min:3',
            'answers' => 'required|array|min:2', 
            'answers.*.answer_text' => 'required|string|min:1',
            'correct_answer_index' => 'required|integer|min:0|max:' . (count($request->answers) - 1),
        ]);
        

        DB::beginTransaction();

        try {

            $question->update(['question_text' => $validated['question_text']]);


            $currentAnswers = $question->answers;
            
            foreach ($validated['answers'] as $index => $answerData) {
                $isCorrect = ($index == $validated['correct_answer_index']);
                
                // Używa istniejącej odpowiedzi na podstawie indeksu lub utwórz nową, jeśli dodano pola
                if (isset($currentAnswers[$index])) {
                    $currentAnswers[$index]->update([
                        'answer_text' => $answerData['answer_text'],
                        'is_correct' => $isCorrect,
                    ]);
                } else {

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
        if ($question->quiz_id !== $quiz->id) {
            abort(404);
        }
        
        $question->delete();

        return redirect()
            ->route('admin.quizzes.questions.index', $quiz)
            ->with('success', 'Pytanie zostało pomyślnie usunięte.');
    }
}