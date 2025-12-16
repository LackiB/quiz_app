<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class QuizCrudController extends Controller
{
    // ... (metody index, create, store, edit, update, destroy dla QUIZU)
    
    public function index(): View
    {
        $quizzes = Quiz::orderBy('id', 'desc')->get();
        return view('admin.quizzes.index', compact('quizzes'));
    }


    public function create(): View
    {
        return view('admin.quizzes.create');
    }

    /**
     * Zapisuje nowy quiz w bazie danych (POST).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Quiz::create($validated);

        return redirect()->route('admin.quizzes.index')->with('success', 'Quiz został pomyślnie utworzony!');
    }

  
    public function edit(Quiz $quiz): View
    {
        $quiz->loadCount('questions'); 
        return view('admin.quizzes.edit', compact('quiz'));
    }

    /**
     * Aktualizuje quiz w bazie danych (PUT/PATCH).
     */
    public function update(Request $request, Quiz $quiz): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $quiz->update($validated);

        return redirect()->route('admin.quizzes.index')->with('success', 'Quiz zaktualizowany.');
    }

   
    public function destroy(Quiz $quiz): RedirectResponse
    {
        $quiz->delete();

        return redirect()->route('admin.quizzes.index')->with('success', 'Quiz usunięty.');
    }
}