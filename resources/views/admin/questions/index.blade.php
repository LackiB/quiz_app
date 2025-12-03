<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pytania dla quizu: ' . $quiz->title) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4 flex justify-between">
                <a href="{{ route('admin.quizzes.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                    &larr; Powrót do Quizów
                </a>
                <a href="{{ route('admin.quizzes.questions.create', $quiz) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    + Dodaj Nowe Pytanie
                </a>
            </div>

            @if ($questions->isEmpty())
                <div class="bg-white p-6 shadow-sm sm:rounded-lg text-gray-600">
                    Ten quiz nie ma jeszcze żadnych pytań.
                </div>
            @else
                @foreach ($questions as $question)
                    <div class="bg-white p-6 shadow-sm sm:rounded-lg mb-4">
                        <p class="text-lg font-bold">{{ $loop->iteration }}. {{ $question->question_text }}</p>
                        <p class="mt-2 font-semibold">Odpowiedzi:</p>
                        <ul class="list-disc ml-5">
                            @foreach ($question->answers as $answer)
                                <li class="{{ $answer->is_correct ? 'text-green-600 font-bold' : 'text-gray-600' }}">
                                    {{ $answer->answer_text }} 
                                    @if ($answer->is_correct) 
                                        (Poprawna) 
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                        <div class="mt-4">
                            {{-- <a href="#" class="text-blue-600 hover:text-blue-900 mr-4">Edytuj</a> --}}
                            {{-- ... Przycisk Usuń ... --}}
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</x-app-layout>