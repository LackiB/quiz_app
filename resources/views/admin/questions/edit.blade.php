<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edytuj Pytanie do quizu: ' . $quiz->title) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 overflow-hidden shadow-sm sm:rounded-lg">
                
                @if ($errors->any())
                    <div class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg">
                        Wystąpiły błędy walidacji!
                    </div>
                @endif
                
                <form method="POST" action="{{ route('admin.quizzes.questions.update', ['quiz' => $quiz, 'question' => $question]) }}">
                    @csrf 
                    @method('PUT') 

                    <div>
                        <x-input-label for="question_text" :value="__('Treść Pytania')" />
                        <x-text-input 
                            id="question_text" 
                            class="block mt-1 w-full" 
                            type="text" 
                            name="question_text" 
                            :value="old('question_text', $question->question_text)" 
                            required 
                            autofocus 
                        />
                        <x-input-error class="mt-2" :messages="$errors->get('question_text')" />
                    </div>
                    
                    <h3 class="text-xl font-semibold mt-6 mb-4">Odpowiedzi i Poprawna Wersja</h3>
                    
                    @foreach ($question->answers as $index => $answer)
                        <div class="flex items-center mt-4 space-x-4">
                            <div class="flex-grow">
                                <x-input-label for="answer_{{ $index }}" :value="__('Odpowiedź ' . ($index + 1))" />
                                <x-text-input 
                                    id="answer_{{ $index }}" 
                                    class="block mt-1 w-full" 
                                    type="text" 
                                    name="answers[{{ $index }}][answer_text]" 
                                    :value="old('answers.' . $index . '.answer_text', $answer->answer_text)"
                                    required 
                                />
                                <x-input-error class="mt-2" :messages="$errors->get('answers.' . $index . '.answer_text')" />
                            </div>

                            <div class="pt-6">
                                <label for="correct_{{ $index }}" class="flex items-center">
                                    <input 
                                        id="correct_{{ $index }}" 
                                        type="radio" 
                                        name="correct_answer_index" 
                                        value="{{ $index }}"
                                        @checked(old('correct_answer_index', $correctAnswerIndex) == $index)
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                        required
                                    >
                                    <span class="ms-2 text-sm text-gray-600">Poprawna</span>
                                </label>
                            </div>
                        </div>
                    @endforeach
                    
                    <x-input-error class="mt-2" :messages="$errors->get('correct_answer_index')" />


                    <div class="flex items-center justify-end mt-8">
                        <x-primary-button>
                            {{ __('Zapisz Zmiany') }}
                        </x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>