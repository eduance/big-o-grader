<?php

namespace App\Grader;

use Illuminate\Support\Collection;

class ExcelDataProvider implements GradeDataProvider
{
    /**
     * Initialize a new data provider instance.
     *
     * @return void
     */
    public function __construct(
        protected Collection $questions,
        protected Collection $results
    )
    {
    }

    public function getData(): mixed
    {
        return [
            $this->questions,
            $this->results
        ];
    }
}
