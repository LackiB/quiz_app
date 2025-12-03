<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            // Klucz obcy do tabeli 'users' (Temat 8)
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
            // Klucz obcy do tabeli 'quizzes'
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
            
            $table->integer('score'); // Liczba poprawnych odpowiedzi
            $table->integer('total_questions'); // Całkowita liczba pytań w quizie
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
