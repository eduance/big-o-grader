<?php

namespace App\Grader;
interface GradeDataProvider
{
    /**
     * Get the data from the current provider.
     *
     * @return mixed
     */
    public function getData(): mixed;
}
