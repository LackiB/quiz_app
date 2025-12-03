<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    use HasFactory;
    
    // Umożliwia masowe przypisywanie (Mass Assignment) dla tych kolumn
    protected $fillable = ['title', 'description'];

    /**
     * Quiz ma wiele pytań.
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }
}