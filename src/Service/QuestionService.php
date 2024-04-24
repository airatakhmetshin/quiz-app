<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\{ResultDto, SessionProgressDto, SubmitQuestionDto};
use App\Entity\{AnswerEntity, QuestionEntity, QuestionResultEntity};
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

        $status = $this->answerRepository
            ->successOrFailed(
                questionID: $questionID,
                answerIDs: $answerIDs,
            );

        $newResult->setStatus($status);

        $this->em->persist($newResult);
        $this->em->flush();
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
