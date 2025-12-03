<?php

namespace Database\Seeders;

use App\Models\Quiz;
use Illuminate\Database\Seeder;


class QuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Tworzenie pierwszego quizu
        $quizLaravel = Quiz::create([
            'title' => 'Podstawy Laravela i MVC',
            'description' => 'Quiz sprawdzający wiedzę z zakresu routingu i modeli.',
        ]);

        // 2. Dodawanie pytań i odpowiedzi do quizu Laravel
        $question1 = $quizLaravel->questions()->create([
            'question_text' => 'Co oznacza skrót MVC?',
        ]);

        $question1->answers()->createMany([
            ['answer_text' => 'Model View Controller', 'is_correct' => true],
            ['answer_text' => 'Module View Component', 'is_correct' => false],
            ['answer_text' => 'Main Virtual Client', 'is_correct' => false],
        ]);

        $question2 = $quizLaravel->questions()->create([
            'question_text' => 'Który plik w Laravelu służy do definiowania tras webowych?',
        ]);

        $question2->answers()->createMany([
            ['answer_text' => 'routes/api.php', 'is_correct' => false],
            ['answer_text' => 'routes/web.php', 'is_correct' => true],
            ['answer_text' => 'config/app.php', 'is_correct' => false],
        ]);
        
        // Możesz dodać więcej quizów, jak wcześniej
        Quiz::create(['title' => 'HTML & CSS dla początkujących', 'description' => '...']);
        Quiz::create(['title' => 'Quiz o JavaScript', 'description' => '...']);
    }
}
