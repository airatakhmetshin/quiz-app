<?php

namespace App\Repository;

use App\Dto\ResultDto;
use App\Dto\SessionProgressDto;
use App\Entity\QuestionResultEntity;
use App\Enum\QuestionResultStatusEnum;
use App\SessionStorage\QuizSession;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<QuestionResultEntity>
 *
 * @method QuestionResultEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method QuestionResultEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method QuestionResultEntity[]    findAll()
 * @method QuestionResultEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionResultRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuestionResultEntity::class);
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

        $row = $this->getEntityManager()
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
order by qr.id asc
SQL;

        $rows = $this->getEntityManager()
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
