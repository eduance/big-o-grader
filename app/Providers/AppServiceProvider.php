<?php

namespace App\Providers;

use App\Caesura;
use App\Grader\GradeAlgorithm;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(GradeAlgorithm::class, Caesura::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
