<?php

namespace App\Grader;
use Illuminate\Support\Fluent;

interface GradeAlgorithm
{
    /**
     * Execute the given algorithm and return the results.
     *
     * @param GradeDataProvider $provider
     * @return mixed
     */
    public function calculate(GradeDataProvider $provider): mixed;
}
