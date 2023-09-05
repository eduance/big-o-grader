<?php

namespace App;
use App\Grader\GradeAlgorithm;
use App\Grader\GradeDataProvider;
use Illuminate\Support\Fluent;

class Caesura implements GradeAlgorithm
{
    /**
     * The Caesura Algorithm grades students based on their performance.
     *
     * Grading Rules:
     * - The grade ranges between 1.0 and 10.0 (with 1 decimal).
     * - When the student scores 20% (or less) of the available points, they receive a 1.0.
     * - When a student scores 70% of the available points, they receive a 5.5 and pass the exam.
     * - When a student scores 100% of the available points, they receive a perfect score of 10.0.
     *
     * We use linear interpolation with reference points (20, 1.0) and (70, 5.5) for all grades under 70 points.
     * And we use reference points (100, 10.0) and (70, 5.5) for all grades above 70 points.
     *
     * @param GradeDataProvider $provider
     * @return mixed
     */
    public function calculate(GradeDataProvider $provider): mixed
    {
        [$questions, $results] = $provider->getData();

        $totalSumOfScorePerQuestion = collect(array_fill_keys(range(1, $questions->count()), 0));
        $totalMaxScore = collect($questions)->sum->getScore();
        $totalResultsCount = $results->count();
        $totalSumGrades = 0;

        $sumX2s = collect(array_fill_keys(range(1, $questions->count()), 0));
        $sumXYs = collect(array_fill_keys(range(1, $questions->count()), 0));
        $sumY2=0;

        $grades = $results->map(function ($scores) use ($totalMaxScore, $totalSumOfScorePerQuestion, &$sumX2s, $sumXYs, &$sumY2, &$totalSumGrades) {
            $totalObtainedScore = 0;

            $scores->each(function ($score, $index) use (&$totalObtainedScore, &$sumX2s, &$totalSumOfScorePerQuestion) {
                $totalSumOfScorePerQuestion[$index] = ($totalSumOfScorePerQuestion[$index]) + $score;
                $totalObtainedScore += $score;

                // Calculate Σx^2 for each question
                $sumX2s[$index] += $score ** 2;
            });

            $grade = Grade::fromScore($totalObtainedScore, $totalMaxScore)->value;
            $totalSumGrades += $grade;

            $sumY2 += $grade * $grade;

            $scores->each(function ($score, $index) use ($grade, &$sumXYs) {
                $sumXYs[$index] += $score * $grade;
            });

            return $grade;
        });

        return (new Fluent())
            ->withGrades($grades)
            ->withMaxScore($totalMaxScore)
            ->withSumScorePerQuestion($totalSumOfScorePerQuestion)
            ->withResultCount($totalResultsCount)
            ->withSumX2($sumX2s)
            ->withSumY2($sumY2)
            ->withSumXY($sumXYs)
            ->withGradeSum($totalSumGrades);
    }
}
