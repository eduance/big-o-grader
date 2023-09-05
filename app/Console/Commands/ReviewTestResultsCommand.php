<?php

namespace App\Console\Commands;

use App\Imports\GradesImport;
use App\Question;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'testresults:review')]
class ReviewTestResultsCommand extends Command
{
    protected $signature = 'testresults:review {file? : The name of results file we are reviewing}';
    protected $description = 'Process an input list of student results.';

    public function handle(): void
    {
        try {
            $importer = new GradesImport();
            $importer->import($this->argument('file'));

            $result = $importer->getResult();
            $questions = $importer->getQuestions();
            $studentIds = $importer->getStudents();

            $choice = $this->choice('What would you like to do?', ['View grades', 'View question analysis']);

            match ($choice) {
                'View grades' => $this->displayGrades($result, $studentIds),
                'View question analysis' => $this->displayQuestionAnalysis($questions),
                default => $this->error('Invalid choice!'),
            };

        } catch (\Exception $exception) {
            throw new \Exception("Oops, something went wrong.." . $exception->getMessage());
        }
    }

    private function displayGrades($result, $studentIds)
    {
        $headers = ["Student ID", "Grade"];
        $studentHashmap = [];
        $grades = $result->get('withGrades')->values();

        foreach ($grades as $index => $grade) {
            $studentHashmap[] = [
                'Student ID' => $studentIds[$index],
                'Grade' => $grade
            ];
        }

        $this->table($headers, $studentHashmap);

        $studentId = $this->ask('<question>What user are you looking for? (Or type "all" to list all)</question>');

        if ($studentId === "all") {
            $this->table($headers, $studentHashmap);
        } elseif (isset($studentHashmap[$studentId - 1])) {
            $grade = $studentHashmap[$studentId - 1];

            $scoreComment = match (true) {
                $grade['Grade'] < 5.5 => 'unfortunate',
                $grade['Grade'] < 6 => 'decent',
                default => 'whopping',
            };

            $this->line('------------------------------------------');
            $this->info(sprintf('<info>The user %s has scored a %s grade of %s</info>', $studentId, $scoreComment, $grade['Grade']));
            $this->line('------------------------------------------');
        } else {
            $this->error('Oops! We couldn\'t find the specified student. Try again with a valid student ID.');
        }
    }

    private function displayQuestionAnalysis($questions)
    {
        $questionHeaders = ["Question Text", "Max Score", "Average Score", "P-Value", "Correlation"];

        $questionInfo = $questions->map(function (Question $question) {
            return [
                'Question Text' => $question->getText(),
                'Max Score' => $question->getScore(),
                'Average Score' => $question->getAverageScore(),
                'P-Value' => $question->getPvalue(),
                'Correlation' => $question->getRitValue()
            ];
        })->toArray();

        $this->table($questionHeaders, $questionInfo);
    }
}
