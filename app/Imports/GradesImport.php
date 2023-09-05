<?php

namespace App\Imports;

use App\Caesura;
use App\Grader\ExcelDataProvider;
use App\Grader\Grader;
use App\Question;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;

class GradesImport implements ToCollection
{
    use Importable;

    /**
     * The processed grader result.
     *
     * @var
     */
    protected $result;

    /**
     * The processed questions.
     *
     * @var
     */
    protected $questions;

    /**
     * Get a list of all students.
     *
     * @var
     */
    protected $students;

    /**
     * Hydrate the given import to question objects.
     * Time complexity is O(n), where n is the length of questionTexts or the number of rows (whichever is larger).
     *
     * @TODO Separate hydration from the import process.
     * @TODO Consider chunking for larger datasets.
     *
     * @param Collection $rows
     * @return Collection
     * @throws Exception
     */
    public function collection(Collection $rows): Collection
    {
        $studentIds = $rows->skip(2)->pluck(0);
        $this->setStudents($studentIds);

        $questionTexts = $rows->get(0)->forget(0);
        $scores = $rows->get(1)->forget(0);

        $hydratedQuestions = $this->hydrateQuestions($questionTexts, $scores);

        $resultsWithoutColumnA = $rows->skip(2)->map(function ($result) {
            return $result->forget(0);
        });

        $algorithm = new Caesura();
        $provider = new ExcelDataProvider($hydratedQuestions, $resultsWithoutColumnA);

        $graderResult = Grader::make()
                ->from($provider)
                ->using($algorithm)
                ->grade();

        $this->setResult($graderResult);

        $sumY = $graderResult->get('withGradeSum');
        $sumY2 = $graderResult->get('withSumY2');

        $hydratedQuestions->map(function ($question, $index) use ($graderResult, $scores, $sumY, $sumY2) {
            $totalObtainedScoreForQuestion = $graderResult->get('withSumScorePerQuestion')->get($index);
            $totalStudentCount = $graderResult->get('withResultCount');

            $averageScore = $totalObtainedScoreForQuestion / $totalStudentCount;
            $pValue = $averageScore / $question->getScore();

            $sumX = $totalObtainedScoreForQuestion;
            $sumX2 = $graderResult->get('withSumX2')->get($index);
            $sumXY = $graderResult->get('withSumXY')->get($index);

            $numerator = ($totalStudentCount * $sumXY) - ($sumX * $sumY);
            $denominator = sqrt(($totalStudentCount * $sumX2 - $sumX ** 2) * ($totalStudentCount * $sumY2 - $sumY ** 2));
            $ritValue = ($denominator == 0) ? 0 : $numerator / $denominator;

            $question->setAverageScore($averageScore);
            $question->setPValue($pValue);
            $question->setRitValue($ritValue);
        });

        $this->questions = $hydratedQuestions;

        return collect();
    }

    /**
     * Set the result.
     *
     * @return void
     */
    protected function setResult(Fluent $result)
    {
        $this->result = $result;
    }

    /**
     * Get the result.
     *
     * @return Fluent
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Set the questions.
     *
     * @return void
     */
    protected function setQuestions(Collection $questions)
    {
        $this->questions = $questions;
    }

    /**
     * Set the students.
     *
     * @return void
     */
    protected function setStudents(Collection $students)
    {
        $this->students = $students;
    }

    /**
     * Get the questions.
     *
     * @return Collection
     */
    public function getQuestions()
    {
        return $this->questions;
    }

    /**
     * Get a collection of all student IDs.
     *
     * @return Collection
     */
    public function getStudents()
    {
        return $this->students;
    }

    /**
     * Hydrate question objects from question texts and scores.
     * Time complexity is O(n), where n is the length of the question texts.
     *
     * @param Collection $questionTexts
     * @param Collection $scores
     * @return Collection
     */
    private function hydrateQuestions(Collection $questionTexts, Collection $scores): Collection
    {
        return $questionTexts->map(function ($question, $index) use ($scores) {
            $score = $scores->get($index);
            return new Question(text: $question, score: $score);
        });
    }
}
