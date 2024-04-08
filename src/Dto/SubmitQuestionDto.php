<?php

declare(strict_types=1);

namespace App\Dto;

use App\SessionStorage\QuizSession;

final readonly class SubmitQuestionDto
{
    public array $answersIDs;

    public function __construct(
        public QuizSession $quizSession,
        public int         $questionID,
        array              $answerIDs,
    ) {
        foreach ($answerIDs as $answerID) {
            if (is_int($answerID)) {
                continue;
            }

            throw new \TypeError('This value should be of type integer');
        }

        $this->answersIDs = $answerIDs;
    }
}
