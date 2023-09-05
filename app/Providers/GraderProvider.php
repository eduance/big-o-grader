<?php

namespace App\Providers;

use App\Caesura;
use App\Grader\GradeAlgorithm;
use Illuminate\Support\ServiceProvider;

class GraderProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(GradeAlgorithm::class, Caesura::class);
    }
}
