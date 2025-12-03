<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Tutaj nie dodawaliśmy żadnego kodu w naszym projekcie.
        // Domyślnie ten blok jest często pusty lub zawiera minimalne ustawienia.
    }
}
