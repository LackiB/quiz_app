<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $quiz->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                
                <h3 class="text-2xl font-bold mb-6 text-gray-900">
                    Pytanie {{ $questionNumber }} z {{ $totalQuestions }}: {{ $question->question_text }}
                </h3>

                <form method="POST" action="{{ route('quiz.submit') }}">
                    @csrf <input type="hidden" name="quiz_id" value="{{ $quiz->id }}">
                    <input type="hidden" name="question_id" value="{{ $question->id }}">

        @if ($errors->any())
            <div class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                Proszę wybrać odpowiedź, aby przejść dalej.
            </div>
        @endif

        <h3 class="text-2xl font-bold mb-6 text-gray-900">
                    <div class="space-y-4 mb-6">
                        @foreach ($answers as $answer)
                            <label for="answer_{{ $answer->id }}" class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-indigo-50 transition duration-150 ease-in-out">
                                <input id="answer_{{ $answer->id }}" 
                                       type="radio" 
                                       name="answer_id" 
                                       value="{{ $answer->id }}" 
                                       class="form-radio h-5 w-5 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                <span class="ml-3 text-lg text-gray-700">{{ $answer->answer_text }}</span>
                            </label>
                        @endforeach
                    </div>

                    <div class="mt-6 flex justify-end">
                        <x-primary-button>
                            Następne pytanie
                        </x-primary-button>
                    </div>
                </form>
                
            </div>
        </div>
    </div>
</x-app-layout>