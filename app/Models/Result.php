<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Result extends Model
{
    use HasFactory;
    
    // Zezwolenie na masowe przypisywanie dla tych kolumn
    protected $fillable = ['user_id', 'quiz_id', 'score', 'total_questions'];

    /**
     * Wynik należy do jednego użytkownika.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Wynik należy do jednego quizu.
     */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }
}