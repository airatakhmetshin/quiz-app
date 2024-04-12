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
        $sql = <<<SQL
with question_count as (
    select count(*) as count from question q
),
question_result_count as (
    select count(*) as count from question_result qr where qr.session_id = :session_id
)
select
    (select count from question_count) as total,
    (select count from question_result_count) as progress
SQL;

        $row = $this->em
            ->getConnection()
            ->executeQuery($sql, ['session_id' => $quizSession->getID()])
            ->fetchAssociative();

        return new SessionProgressDto(
            total: $row['total'],
            progress: $row['progress'],
        );
    }

    /**
     * @return ResultDto[]
     * @throws \Doctrine\DBAL\Exception
     */
    public function getResult(QuizSession $quizSession): array
    {
        $sql = <<<SQL
select qr.id, qr.question_id, qr.status, q.text
from question_result qr
left join question q on qr.question_id = q.id
where qr.session_id = :session_id
order by qr.question_id asc
SQL;

        $rows = $this->em
            ->getConnection()
            ->executeQuery($sql, ['session_id' => $quizSession->getID()])
            ->fetchAllAssociative();

        return array_map(static fn(array $row) => new ResultDto(
            id: $row['id'],
            status: QuestionResultStatusEnum::from($row['status']),
            questionID: $row['question_id'],
            questionText: $row['text'],
        ), $rows);
    }
}
