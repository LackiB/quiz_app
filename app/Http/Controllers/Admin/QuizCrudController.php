<?php

namespace App\Http\Controllers\Admin; // Prawidłowa przestrzeń nazw

use App\Http\Controllers\Controller;
use App\Models\Quiz; // Import modelu Quiz
use Illuminate\Http\Request;
use Illuminate\View\View;
// !!! USUNĄŁEM: use App\Http\Controllers\Admin; !!!

class QuizCrudController extends Controller
{
    /**
     * Wyświetla listę quizów do zarządzania.
     */
    public function index(): View
    {
        $quizzes = Quiz::orderBy('id', 'desc')->get();
        return view('admin.quizzes.index', compact('quizzes'));
    }

    /**
     * Pokazuje formularz do tworzenia nowego quizu.
     */
    public function create(): View
    {
        return view('admin.quizzes.create');
    }

    /**
     * Zapisuje nowy quiz w bazie danych (POST).
     */
    public function store(Request $request)
    {
        // Walidacja (Temat 6)
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Quiz::create($validated);

        return redirect()->route('admin.quizzes.index')->with('success', 'Quiz został pomyślnie utworzony!');
    }

    // Dodajmy brakujące metody CRUD, by nie było kolejnych błędów
    
    /**
     * Pokazuje formularz do edycji quizu.
     */
    public function edit(Quiz $quiz): View
    {
        return view('admin.quizzes.edit', compact('quiz'));
    }

    /**
     * Aktualizuje quiz w bazie danych (PUT/PATCH).
     */
    public function update(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $quiz->update($validated);

        return redirect()->route('admin.quizzes.index')->with('success', 'Quiz zaktualizowany.');
    }

    /**
     * Usuwa quiz (DELETE).
     */
    public function destroy(Quiz $quiz)
    {
        $quiz->delete();

        return redirect()->route('admin.quizzes.index')->with('success', 'Quiz usunięty.');
    }
}