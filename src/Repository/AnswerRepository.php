<?php

namespace App\Repository;

use App\Entity\AnswerEntity;
use App\Enum\QuestionResultStatusEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AnswerEntity>
 *
 * @method AnswerEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method AnswerEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method AnswerEntity[]    findAll()
 * @method AnswerEntity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnswerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AnswerEntity::class);
    }

    /**
     * @param array<int> $answerIDs
     * @throws \Doctrine\DBAL\Exception
     */
    public function successOrFailed(int $questionID, array $answerIDs): QuestionResultStatusEnum
    {
        $sql = <<<SQL
with selected_answers as (
    select id from answer where id in (:answer_ids)
)
select
    a.id,
    a.is_correct,
    case when sa.id is not null
        then true else false
    end as is_selected
from answer a
left join selected_answers sa on sa.id = a.id
where a.question_id = :question_id
SQL;

        $rows = $this->getEntityManager()
            ->getConnection()
            ->executeQuery($sql, [
                'question_id' => $questionID,
                'answer_ids'  => $answerIDs,
            ], [
                'answer_ids' => ArrayParameterType::INTEGER,
            ])
            ->fetchAllAssociative();

        foreach ($rows as $row) {
            if ($row['is_correct'] === $row['is_selected']) {
                continue;
            }

            return QuestionResultStatusEnum::FAILED;
        }

        return QuestionResultStatusEnum::SUCCESS;
    }
}
