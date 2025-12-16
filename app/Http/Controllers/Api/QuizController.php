<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse; 

class QuizController extends Controller
{
    /**
     * Zwraca listę wszystkich quizów. (GET /api/quizzes)
     */
    public function index(): JsonResponse
    {
        $quizzes = Quiz::all();
        
        return response()->json($quizzes);
    }


    public function store(Request $request): JsonResponse
    {

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);


        $quiz = Quiz::create($validatedData);
        return response()->json($quiz, 201);
    }

    /**
     * Zwraca szczegóły pojedynczego quizu. (GET /api/quizzes/{quiz})
     * Używamy Route Model Binding, aby Laravel automatycznie znalazł quiz.
     */
    public function show(Quiz $quiz): JsonResponse
    {
        $quiz->load('questions.answers');

        return response()->json($quiz);
    }


    public function update(Request $request, Quiz $quiz): JsonResponse
    {

        $validatedData = $request->validate([
            'title' => 'sometimes|required|string|max:255', 
            'description' => 'nullable|string',
        ]);


        $quiz->update($validatedData);


        return response()->json($quiz);
    }

    /**
     * Usuwa quiz. (DELETE /api/quizzes/{quiz})
     */
    public function destroy(Quiz $quiz): JsonResponse
    {
        $quiz->delete();
        return response()->json(null, 204);
    }
}