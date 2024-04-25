<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\{ResultDto, SessionProgressDto, SubmitQuestionDto};
use App\Entity\{AnswerEntity, QuestionEntity, QuestionResultEntity};
use App\Enum\QuestionResultStatusEnum;
use App\Repository\{AnswerRepository, QuestionRepository, QuestionResultRepository};
use App\SessionStorage\QuizSession;
use Doctrine\ORM\EntityManagerInterface;

class QuestionService
{
    public function __construct(
        protected EntityManagerInterface   $em,
        protected AnswerRepository         $answerRepository,
        protected QuestionRepository       $questionRepository,
        protected QuestionResultRepository $questionResultRepository,
    ) {
    }

    /**
     * @return array{question: QuestionEntity, answers: AnswerEntity[]}
     */
    public function getQuestion(QuizSession $quizSession, bool $answerShuffle = false): array
    {
        $question = $this->questionRepository
            ->getNewRandomQuestion(sessionID: $quizSession->getID());

        $answers = $question?->getAnswers()->toArray();

        if ($answerShuffle) {
            shuffle($answers);
        }

        return [
            'question' => $question,
            'answers'  => $answers,
        ];
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function submit(SubmitQuestionDto $submitQuestion): void
    {
        $quizSession = $submitQuestion->quizSession;
        $questionID  = $submitQuestion->questionID;
        $answerIDs   = $submitQuestion->answersIDs;

        $newResult = new QuestionResultEntity();
        $newResult->setSessionId($quizSession->getID());
        $newResult->setQuestionId($questionID);
        $newResult->setAnswerIds($answerIDs);

        $status = $this->successOrFailed(
            questionID: $questionID,
            answerIDs: $answerIDs,
        );

        $newResult->setStatus($status);

        $this->em->persist($newResult);
        $this->em->flush();
    }

    protected function successOrFailed(int $questionID, array $answerIDs): QuestionResultStatusEnum
    {
        $answers = $this->answerRepository->findBy(['question' => $questionID]);

        $rows = array_map(static fn(AnswerEntity $answer): array => [
            'id'          => $answer->getId(),
            'is_correct'  => $answer->isCorrect(),
            'is_selected' => in_array($answer->getId(), $answerIDs, true),
        ], $answers);

        $correctedCount = count(array_filter($rows, static fn(array $row) => $row['is_correct']));
        $successCount   = 0;

        foreach ($rows as $row) {
            // is_correct === true
            if ($row['is_correct']) {
                if ($row['is_selected']) {
                    $successCount++;
                }
            // is_correct === false
            } else {
                if ($row['is_selected']) {
                    $successCount = -1;

                    break;
                }
            }
        }

        return match (true) {
            $correctedCount > 0 && $successCount > 0     => QuestionResultStatusEnum::SUCCESS,
            $correctedCount === 0 && $successCount === 0 => QuestionResultStatusEnum::SUCCESS,
            default                                      => QuestionResultStatusEnum::FAILED,
        };
    }

    public function hasQuestions(QuizSession $quizSession): bool
    {
        ['question' => $question] = $this->getQuestion($quizSession);

        return $question instanceof QuestionEntity;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function hasActiveQuizSession(QuizSession $quizSession): SessionProgressDto
    {
        return $this->questionResultRepository
            ->hasActiveQuizSession($quizSession);
    }

    /**
     * @return ResultDto[]
     * @throws \Doctrine\DBAL\Exception
     */
    public function getResult(QuizSession $quizSession): array
    {
        return $this->questionResultRepository
            ->getResult($quizSession);
    }
}
