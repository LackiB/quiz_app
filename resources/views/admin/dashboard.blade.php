<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Panel Administracyjny') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-2xl font-bold mb-4">Witaj w Edytorze Quizów!</h3>
                    
                    <p class="mb-6">Wybierz, co chcesz edytować:</p>

                    <a href="{{ route('admin.quizzes.index') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-indigo-700 transition ease-in-out duration-150">
                        Zarządzaj Quizami
                    </a>
                    
                </div>
            </div>
        </div>
    </div>
</x-app-layout>