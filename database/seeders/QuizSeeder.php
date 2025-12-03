<?php

namespace Database\Seeders;

use App\Models\Quiz;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Dodajemy przykładowe quizy
        Quiz::create([
            'title' => 'Podstawy Laravela i MVC',
            'description' => 'Quiz sprawdzający wiedzę z zakresu routingu i modeli.',
        ]);

        Quiz::create([
            'title' => 'HTML & CSS dla początkujących',
            'description' => 'Quiz dotyczący budowy i stylizacji stron internetowych.',
        ]);

        Quiz::create([
            'title' => 'Quiz o JavaScript',
            'description' => 'Sprawdź swoją znajomość JavaScript.',
        ]);
    }
}