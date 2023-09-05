<?php

namespace App;

use App\ValueObjects\Percent;

final class Grade
{
    const MAX_GRADE = 10.0;
    const MAX_GRADE_SCORE = 100;
    const PASSING_GRADE_SCORE = 70;
    const PASSING_GRADE = 5.5;
    CONST MIN_GRADE = 1.0;
    CONST MIN_GRADE_SCORE = 20;

    /**
     * Create a new grade instance.
     *
     * @param float $value
     */
    private function __construct(public readonly float $value)
    {
    }

    /**
     * Create a new grade instance from a float.
     *
     * @param float $grade
     * @return Grade
     */
    public static function from(float $grade): Grade
    {
        return new self(round($grade, 1));
    }

    /**
     * Create a new grade instance from a given score.
     *
     * @param float $score
     * @param float $maxScore
     * @return Grade
     */
    public static function fromScore(float $score, float $maxScore): self
    {
        $percentage = Percent::from($score, $maxScore);

        return self::fromPercentage($percentage->value);
    }

    /**
     * Create a new grade instance from a given percentage.
     *
     * @param float $percentage
     * @return Grade
     */
    public static function fromPercentage(float $percentage): self
    {
        if ($percentage >= 70) {
            return Grade::from(
                self::linearInterpolation(
                    $percentage,
                    x0: self::MAX_GRADE_SCORE,
                    x1: self::PASSING_GRADE_SCORE,
                    y0: self::MAX_GRADE,
                    y1: self::PASSING_GRADE
                )
            );
        }

        if ($percentage > 20) {
            return Grade::from(
                self::linearInterpolation(
                    $percentage,
                    x0: self::MIN_GRADE_SCORE,
                    x1: self::PASSING_GRADE_SCORE,
                    y0: self::MIN_GRADE,
                    y1: self::PASSING_GRADE
                )
            );
        }

        return Grade::from(self::MIN_GRADE);
    }

    /**
     * Calculate using linear interpolation.
     *
     * @param float $x
     * @param float $x0
     * @param float $x1
     * @param float $y0
     * @param float $y1
     * @return float
     */
    protected static function linearInterpolation(
        float $x,
        float $x0,
        float $x1,
        float $y0,
        float $y1
    ): float
    {
        return $y0 + ($y1 - $y0) * (($x - $x0) / ($x1 - $x0));
    }
}
