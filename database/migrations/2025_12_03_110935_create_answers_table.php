<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnswersTable extends Migration
{
    public function up(): void
    {
        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            // Klucz obcy: 'question_id' odwołujący się do tabeli 'questions'
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->string('answer_text');
            // Kolumna typu boolean, domyślnie false (fałsz)
            $table->boolean('is_correct')->default(false); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('answers');
    }
};
