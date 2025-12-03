<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dostępne Quizy') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if($quizzes->isEmpty())
                        <p>Brak dostępnych quizów.</p>
                    @else
                        <h3 class="text-lg font-medium mb-4">Wybierz quiz:</h3>
                        
                        <div class="space-y-4">
                            @foreach($quizzes as $quiz)
                                @if ($quiz->first_question_id)
                                        <a href="{{ route('quiz.show_next', ['quiz' => $quiz->id, 'question' => $quiz->first_question_id]) }}" 
                                            class="block p-4 border rounded-lg hover:bg-gray-50 transition duration-150 ease-in-out">
                                        <h4 class="text-xl font-bold text-indigo-600">{{ $quiz->title }}</h4> 
                                        <p class="text-sm text-gray-600 mt-1">{{ $quiz->description }}</p>
                                        <span class="mt-2 inline-block text-xs font-semibold text-white bg-indigo-500 px-3 py-1 rounded-full">Rozpocznij Quiz</span>
                                    </a>
                                @else
                                    <p>Quiz tymczasowo niedostępny.</p>
                                @endif
                            @endforeach
                        </div>
                    @endif
                    
                </div>
            </div>
        </div>
    </div>
</x-app-layout>