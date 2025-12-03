<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse; // Używamy, aby jasno określić typ odpowiedzi

class QuizController extends Controller
{
    /**
     * Zwraca listę wszystkich quizów. (GET /api/quizzes)
     */
    public function index(): JsonResponse
    {
        // Używamy metody all() z modelu Eloquent
        $quizzes = Quiz::all();
        
        // Zwracamy kolekcję w formacie JSON
        return response()->json($quizzes);
    }

    /**
     * Tworzy nowy quiz. (POST /api/quizzes)
     */
    public function store(Request $request): JsonResponse
    {
        // Walidacja danych przychodzących (Temat 6)
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Tworzenie rekordu w bazie danych
        $quiz = Quiz::create($validatedData);

        // Zwracamy nowo utworzony zasób i status 201 Created
        return response()->json($quiz, 201);
    }

    /**
     * Zwraca szczegóły pojedynczego quizu. (GET /api/quizzes/{quiz})
     * Używamy Route Model Binding, aby Laravel automatycznie znalazł quiz.
     */
    public function show(Quiz $quiz): JsonResponse
    {
        // Ładujemy relację pytań, aby były dostępne w odpowiedzi JSON
        $quiz->load('questions.answers');

        // Zwracamy obiekt w formacie JSON
        return response()->json($quiz);
    }

    /**
     * Aktualizuje istniejący quiz. (PUT/PATCH /api/quizzes/{quiz})
     */
    public function update(Request $request, Quiz $quiz): JsonResponse
    {
        // Walidacja danych (Temat 6)
        $validatedData = $request->validate([
            'title' => 'sometimes|required|string|max:255', // sometimes - wymagane tylko jeśli pole jest w zapytaniu
            'description' => 'nullable|string',
        ]);

        // Aktualizacja rekordu
        $quiz->update($validatedData);

        // Zwracamy zaktualizowany obiekt
        return response()->json($quiz);
    }

    /**
     * Usuwa quiz. (DELETE /api/quizzes/{quiz})
     */
    public function destroy(Quiz $quiz): JsonResponse
    {
        $quiz->delete();

        // Zwracamy pustą odpowiedź ze statusem 204 No Content
        return response()->json(null, 204);
    }
}