<?php

namespace App\Grader;

use Exception;

class Grader
{
    /**
     * The provider we wish to use.
     *
     * @var GradeDataProvider|null
     */
    protected ?GradeDataProvider $provider = null;

    /**
     * The given algorithm.
     *
     * @var GradeAlgorithm|null
     */
    protected ?GradeAlgorithm $algorithm = null;

    /**
     * Create a new Grader instance.
     *
     * @return static
     */
    public static function make(): self
    {
        return new static;
    }

    /**
     * Specify the data provider.
     *
     * @param GradeDataProvider $provider
     * @return $this
     */
    public function from(GradeDataProvider $provider): self
    {
        $this->provider = $provider;
        return $this;
    }

    /**
     * Specify the grading algorithm.
     *
     * @param GradeAlgorithm $algorithm
     * @return $this
     */
    public function using(GradeAlgorithm $algorithm): self
    {
        $this->algorithm = $algorithm;
        return $this;
    }

    /**
     * Grade the test.
     *
     * @return mixed
     * @throws Exception
     */
    public function grade(): mixed
    {
        if (!$this->provider || !$this->algorithm) {
            throw new Exception("Ensure both provider and algorithm are set before grading.");
        }

        return $this->algorithm->calculate($this->provider);
    }
}
