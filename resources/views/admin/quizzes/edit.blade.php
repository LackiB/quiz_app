<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edytuj Quiz: ') . $quiz->title }}
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
                
                <form method="POST" action="{{ route('admin.quizzes.update', $quiz) }}">
                    @csrf 
                    @method('PUT') 

                    <div>
                        <x-input-label for="title" :value="__('Tytuł Quizu')" />
                        <x-text-input 
                            id="title" 
                            class="block mt-1 w-full" 
                            type="text" 
                            name="title" 
                            :value="old('title', $quiz->title)" 
                            required 
                            autofocus 
                        />
                        <x-input-error class="mt-2" :messages="$errors->get('title')" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="description" :value="__('Opis')" />
                        <textarea 
                            id="description" 
                            name="description" 
                            rows="3" 
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full"
                        >{{ old('description', $quiz->description) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('description')" />
                    </div>

                    <div class="flex items-center justify-between mt-8">
                         <a href="{{ route('admin.quizzes.questions.index', $quiz) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold underline">
                            Zarządzaj Pytaniami ({{ $quiz->questions_count ?? 0 }})
                        </a>

                        <x-primary-button class="ms-4">
                            {{ __('Zapisz Zmiany Quizu') }}
                        </x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>