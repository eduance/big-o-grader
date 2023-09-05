<?php

namespace App;
class Question
{
    /**
     * The calculated p-value.
     *
     * @var float
     */
    protected float $pvalue;

    /**
     * The calculated correlation value calculated using the Pearson Correlation Coeffecient.
     *
     * @var float
     */
    protected float $ritValue;

    /**
     * The average score for a question.
     *
     * @var float
     */
    protected float $averageScore;

    /**
     * Initialize a new question object.
     *
     * @param string $text
     * @param int $score
     */
    public function __construct(
        protected string $text,
        protected int $score,
    )
    {
    }

    /**
     * Get the max available points for a question.
     *
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * Get the max available points for a question.
     *
     * @return int
     */
    public function getScore(): int
    {
        return $this->score;
    }

    /**
     * The given p-value.
     *
     * @return float
     */
    public function getPvalue(): float
    {
        return $this->pvalue;
    }

    /**
     * The given p-value.
     *
     * @param float $value
     * @return void
     */
    public function setPValue(float $value): void
    {
        $this->pvalue = $value;
    }

    /**
     * Get the Rit value.
     *
     * @return float
     */
    public function getRitValue()
    {
        return $this->ritValue;
    }

    /**
     * The average score for question based on the total respondent count.
     *
     * @return float
     */
    public function getAverageScore(): float
    {
        return $this->averageScore;
    }

    /**
     * The given p-value.
     *
     * @param float $value
     * @return void
     */
    public function setAverageScore(float $value): void
    {
        $this->averageScore = $value;
    }

    /**
     * The given r-it value.
     *
     * @param float $value
     * @return void
     */
    public function setRitValue(float $value): void
    {
        $this->ritValue = $value;
    }
}
