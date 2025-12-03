<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Wyniki Quizu: ') . $quiz->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8 text-center">
                
                <h3 class="text-3xl font-extrabold mb-4 text-indigo-600">
                    Gratulacje!
                </h3>
                
                <p class="text-6xl font-black mb-6">
                    {{ $score }} / {{ $total }}
                </p>

                <p class="text-xl text-gray-700 mb-8">
                    Poprawnych odpowiedzi na {{ $total }} pytań.
                </p>

                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    Wróć do listy quizów
                </a>
                
            </div>
        </div>
    </div>
</x-app-layout>